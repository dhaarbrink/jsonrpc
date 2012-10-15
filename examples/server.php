<?php

    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);

    spl_autoload_register(function ($class) {
        require_once __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    });

    $server = new \JsonRpc\RpcServer();
    $server->setClass('TestClass');
    $server->addFunction('pi');

    $server->handle();

class TestClass
{
    public function func1()
    {
        throw new \Exception('test');
        $ret = new stdClass();
        $ret->dinges = array('blah' => 'aap');
        return $ret;
    }
}