<?php

    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);

    spl_autoload_register(function ($class) {
        require_once __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    });

    class TestClass
    {
        public function testfunc()
        {
            $ret = new stdClass();
            $ret->foo = array('bar' => 'baz');
            return $ret;
        }
        public function testfunc2()
        {
        	return 42;
        }
        public function throwsException($param)
        {
        	throw new \Exception(sprintf(
        		"The client passed '%s'",
        		$param
        	));
        }
    }

    $server = new \JsonRpc\RpcServer();
    $server->setClass('TestClass');
    $server->handle();
