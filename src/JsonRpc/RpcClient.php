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

class RpcClient
{
    protected $url;
    protected $message_id = 1;
    protected $last_request;
    protected $response_raw;
    protected $batch = false;
    protected $message;
    public function __construct($url)
    {
        $this->url = $url;
    }
    public function __call($method, $arguments)
    {
        $message = $this->createMessage($method, $arguments);

        if ($this->batch) {
        	$this->message[] = $message;
        } else {
        	$this->message = $message;
        }
        
		if (!$this->batch) {
			//dirty trick to use send outside of batch mode
			$this->batch = true;
			$result = $this->send();
			$this->batch = false;
			
			return $result;
		}
		
		//we're in batch mode, return $this so we can chain
		return $this;
    }
    public function batch()
    {
    	$this->batch = true;
    	$this->message = array();
    	return $this;
    }
    public function send()
    {
    	if (!$this->batch) {
    		throw new Exception("Can't use send outside of batch mode");
    	}
    	
        $this->last_request = $this->message;

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => json_encode($this->message)
            )
        );

        $context  = stream_context_create($opts);
        $result = file_get_contents($this->url, false, $context);
        $this->response_raw = $result;
        return json_decode($result);
    }
    public function getLastRequest()
    {
        return $this->last_request;
    }
    public function getResponseRaw()
    {
        return $this->response_raw;
    }
    protected function createMessage($method, $arguments)
    {
        return array(
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $arguments,
            'id' => $this->getMessageId(),
        );
    }
    protected function getMessageId()
    {
        return $this->message_id++;
    }
}
