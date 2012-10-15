<?php
namespace JsonRpc\Exception;

use JsonRpc\Exception;

class InternalErrorException
extends Exception
{
    protected $default_message = "Internal Error";
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $message = (!empty($message)) ? $message : $this->default_message;
        parent::__construct($message, -32603);
    }
}
