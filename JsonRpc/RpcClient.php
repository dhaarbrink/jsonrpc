<?php
namespace JsonRpc;

class RpcClient
{
    protected $url;
    protected $message_id = 1;
    protected $last_request;
    protected $response_raw;
    public function __construct($url)
    {
        $this->url = $url;
    }
    public function __call($method, $arguments)
    {
        $message = $this->createMessage($method, $arguments);
        $this->last_request = $message;

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => json_encode($message)
            )
        );

        $context  = stream_context_create($opts);
        $result = file_get_contents($this->url, false, $context);
        $this->response_raw = $result;
        return json_decode($result);
    }
    public function getLastRequest()
    {
        return $this->last_request;
    }
    public function getResponseRaw()
    {
        return $this->response_raw;
    }
    protected function createMessage($method, $arguments)
    {
        return array(
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $arguments,
            'id' => $this->getMessageId(),
        );
    }
    protected function getMessageId()
    {
        return $this->message_id++;
    }
}
