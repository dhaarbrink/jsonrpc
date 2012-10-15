<?php
namespace JsonRpc;

use JsonRpc\Exception;

class Request
{
    protected $payload;
    protected $messages = array();
    protected $batch = false;

    /**
     * @param $payload
     * @throws Exception\ParseErrorException
     *          Exception\InvalidRequestException
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
    public function parse()
    {
        if (true !== ($result = $this->decode())) {
            throw new Exception\ParseErrorException($result);
        }
        if (true !== ($this->valid())) {
            throw new Exception\InvalidRequestException();
        }
    }
    public function isBatch()
    {
        return $this->batch;
    }
    public function getMessages()
    {
        return $this->messages;
    }
    protected function valid()
    {
        $payload = &$this->payload;
        if (!is_array($payload) && !is_object($payload)) {
            return false;
        }
        if (is_object($payload)) {
            $payload = array($payload);
            $this->batch = false;
        } else {
            $this->batch = true;
        }
        foreach ($payload as &$request) {
            if (!isset($request->jsonrpc)) return false;
            if ($request->jsonrpc != '2.0') return false;
            if (!isset($request->method)) return false;
            if (!isset($request->params)) {
                $request->params = array();
            }
            if (!is_object($request->params) && !is_array($request->params)) return false;
            if (!isset($request->id)) return false; //don't support notifications for now

            $this->messages[] = $request;
        }

        return true;
    }
    protected function decode()
    {
        if ($this->payload == '') {
            return "No payload received";
        }
        $result = json_decode($this->payload);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return json_last_error();
        }

        $this->payload = $result;

        return true;
    }
}
