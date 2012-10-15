<?php
namespace JsonRpc\Transport;

class BasicInput
implements Input
{
    protected $payload;
    public function getPayload()
    {
        if (null === $this->payload) {
            if (isset($HTTP_RAW_POST_DATA) && !empty($HTTP_RAW_POST_DATA)) {
                $this->payload = $HTTP_RAW_POST_DATA;
            } else {
                $this->payload = file_get_contents('php://input');
            }
        }
        return $this->payload;
    }
}
