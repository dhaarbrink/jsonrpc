<?php
namespace JsonRpc;

class Response
{
    protected $messages = array();
    protected $batch = false;
    public function add($response, $message_id)
    {
        if ($this->isError($response)) {
            $this->messages[] = $this->createErrorResponse($response, $message_id);
        } else {
            $this->messages[] = $this->createResponse($response, $message_id);
        }
    }
    public function getResponse()
    {
        return ($this->batch) ? $this->messages : $this->messages[0];
    }
    public function setBatch($batch)
    {
        $this->batch = $batch;
    }
    protected function isError($response)
    {
        return $response instanceof \Exception;
    }
    protected function createResponse($response, $message_id)
    {
        $struct = array(
            'jsonrpc' => '2.0',
            'result' => $response,
            'id' => $message_id,
        );
        return $struct;
    }
    protected function createErrorResponse(\Exception $response, $message_id)
    {
        $struct = array(
            'jsonrpc' => '2.0',
            'error' => array(
                'code' => $response->getCode(),
                'message' => $response->getMessage(),
                'data' => get_class($response),
            ),
            'id' => $message_id
        );
        return $struct;
    }
}
