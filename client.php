<?php

    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);

    spl_autoload_register(function ($class) {
        require_once __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    });

    $client = new JsonRpc\RpcClient('http://10.8.0.10/jsonrpc/server.php');
//    $result = $client->someMethod('arg1', 2);
    $result = $client->func1('e', 3, 'dennis');
    var_dump($result);
    var_dump($client->getLastRequest());
    var_dump($client->getResponseRaw());
