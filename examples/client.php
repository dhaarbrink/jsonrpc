<?php

    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);

    spl_autoload_register(function ($class) {
        require_once __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    });

    $url = 'http://' . $_SERVER['SERVER_ADDR'] . dirname($_SERVER['REQUEST_URI']) . '/server.php';
    $client = new JsonRpc\RpcClient($url);

    $result = $client->testfunc();
    var_dump($result, $client->getResponseRaw());
    
    $result = $client->batch()
    	->testfunc()
    	->testfunc2()
    	->send();
	var_dump($result, $client->getResponseRaw());
