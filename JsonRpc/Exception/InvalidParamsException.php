<?php
namespace JsonRpc\Exception;

use JsonRpc\Exception;

class InvalidParamsException
extends Exception
{
    protected $default_message = "Invalid Params error";
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $message = (!empty($message)) ? $message : $this->default_message;
        parent::__construct($message, -32602);
    }
}
