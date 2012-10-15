<?php
namespace JsonRpc\Transport;

use JsonRpc\Response;

class BasicOutput
implements Output
{
    public function out(Response $response)
    {
        echo json_encode($response->getResponse());
    }
}
