<?php
namespace JsonRpc\Exception;

use JsonRpc\Exception;

class ServerErrorException
extends Exception
{
    protected $default_message = "Server error";
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $message = (!empty($message)) ? $message : $this->default_message;
        parent::__construct($message, $code);
    }
}
