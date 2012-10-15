<?php
namespace JsonRpc;

use JsonRpc\Transport;

class RpcServer
{
    protected $payload;
    protected $input;
    protected $output;
    protected $error_handler;
    protected $service;
    protected $functions = array();
    public function handle($payload = null, Transport\Input $input = null, Transport\Output $output = null)
    {
        $this->setupErrorHandler();

        $this->input = $input ?: new Transport\BasicInput();
        $this->output = $output ?: new Transport\BasicOutput();
        $payload = $payload ?: $this->input->getPayload();

        $request = new Request($payload);
        $response = new Response();
        $worker = new Worker($this->service, $this->functions);

        try {
            $request->parse();

            foreach ($request->getMessages() as $message) {
                $response->add($worker->handle($message), $message->id);
            }
        } catch (\Exception $e) {
            $response->add($e, $message->id);
        }

        $response->setBatch($request->isBatch());
        $this->output->out($response);

        $this->restoreErrorHandler();
    }
    public function setClass($class)
    {
        $this->service = new $class();
    }
    public function setObject($obj)
    {
        $this->service = $obj;
    }
    public function addFunction($function)
    {
        $this->functions[] = $function;
    }
    public function setupErrorHandler()
    {
        $this->error_handler = set_error_handler(array($this, 'error_handler'));
    }
    public function restoreErrorHandler()
    {
        restore_error_handler($this->error_handler);
    }
    public function error_handler($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        throw new Exception\ServerErrorException($errstr, $errno);
    }
}
