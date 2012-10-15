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

class Response
{
    protected $messages = array();
    protected $batch = false;
    public function add($response, $message_id)
    {
        if ($this->isError($response)) {
            $this->messages[] = $this->createErrorResponse($response, $message_id);
        } else {
            $this->messages[] = $this->createResponse($response, $message_id);
        }
    }
    public function getResponse()
    {
        return ($this->batch) ? $this->messages : $this->messages[0];
    }
    public function setBatch($batch)
    {
        $this->batch = $batch;
    }
    protected function isError($response)
    {
        return $response instanceof \Exception;
    }
    protected function createResponse($response, $message_id)
    {
        $struct = array(
            'jsonrpc' => '2.0',
            'result' => $response,
            'id' => $message_id,
        );
        return $struct;
    }
    protected function createErrorResponse(\Exception $response, $message_id)
    {
        $struct = array(
            'jsonrpc' => '2.0',
            'error' => array(
                'code' => $response->getCode(),
                'message' => $response->getMessage(),
                'data' => get_class($response),
            ),
            'id' => $message_id
        );
        return $struct;
    }
}
