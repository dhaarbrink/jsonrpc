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

use JsonRpc\Exception;

class Request
{
    protected $payload;
    protected $messages = array();
    protected $batch = false;

    /**
     * @param $payload
     * @throws Exception\ParseErrorException
     *          Exception\InvalidRequestException
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
    public function parse()
    {
        if (true !== ($result = $this->decode())) {
            throw new Exception\ParseErrorException($result);
        }
        if (true !== ($this->valid())) {
            throw new Exception\InvalidRequestException();
        }
    }
    public function isBatch()
    {
        return $this->batch;
    }
    public function getMessages()
    {
        return $this->messages;
    }
    protected function valid()
    {
        $payload = &$this->payload;
        if (!is_array($payload) && !is_object($payload)) {
            return false;
        }
        if (is_object($payload)) {
            $payload = array($payload);
            $this->batch = false;
        } else {
            $this->batch = true;
        }
        foreach ($payload as &$request) {
            if (!isset($request->jsonrpc)) return false;
            if ($request->jsonrpc != '2.0') return false;
            if (!isset($request->method)) return false;
            if (!isset($request->params)) {
                $request->params = array();
            }
            if (!is_object($request->params) && !is_array($request->params)) return false;
            if (!isset($request->id)) return false; //don't support notifications for now

            $this->messages[] = $request;
        }

        return true;
    }
    protected function decode()
    {
        if ($this->payload == '') {
            return "No payload received";
        }
        $result = json_decode($this->payload);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return json_last_error();
        }

        $this->payload = $result;

        return true;
    }
}
