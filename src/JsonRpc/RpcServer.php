<?php
/**
 * Copyright 2012 Dennis Haarbrink <dhaarbrink@gmail.com>
 *
 * Permission to use, copy, modify, and distribute this software
 * and its documentation for any purpose and without fee is hereby
 * granted, provided that the above copyright notice appear in all
 * copies and that both that the copyright notice and this
 * permission notice and warranty disclaimer appear in supporting
 * documentation, and that the name of the author not be used in
 * advertising or publicity pertaining to distribution of the
 * software without specific, written prior permission.
 *
 * The author disclaim all warranties with regard to this
 * software, including all implied warranties of merchantability
 * and fitness.  In no event shall the author be liable for any
 * special, indirect or consequential damages or any damages
 * whatsoever resulting from loss of use, data or profits, whether
 * in an action of contract, negligence or other tortious action,
 * arising out of or in connection with the use or performance of
 * this software.
 */
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
