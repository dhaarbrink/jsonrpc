<?php
namespace JsonRpc;

class Worker
{
    protected $service;
    protected $functions;
    protected $map = array();
    public function __construct($service, $functions)
    {
        $this->service = $service;
        $this->functions = $functions;

        $this->map = $this->createCallMap();
    }
    public function handle($message)
    {
        $method = $message->method;
        $params = $message->params;

        if (!isset($this->map[$method])) {
            throw new Exception\MethodNotFoundException();
        }


//        return call_user_func_array($this->map[$method], $params);
        $f = $this->map['func1'];
        return $this->service->func1();
    }
    protected function createCallMap()
    {
        $map = array();
        foreach ($this->functions as $func) {
            if (is_string($func)) {
                $map[$func] = $func;
            } elseif (is_array($func)) {
                $map[$func[1]] = $func;
            }
        }
        if (is_object($this->service)) {
            foreach (get_class_methods($this->service) as $func) {
                $map[$func] = array($this->service, $func);
            }
        }
        return $map;
    }
}
