<?php

namespace App\Router;

class Router {
    private $request;
    private $supportedHttpMethods = ["GET", "POST"];

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    function __call($name, $args)
    {
        list($route, $method) = $args;
        if(!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }

        $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
    }

    private function formatRoute($route)
    {
        $result = rtrim($route, '/');
        if ($result === '') {
            return '/';
        }
        return $result;
    }

    private function invalidMethodHandler()
    {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    private function defaultRequestHandler()
    {
        header("{$this->request->serverProtocol} 404 Not Found");
    }

    function resolve()
    {
        try {
            if (!isset($this->{strtolower($this->request->requestMethod)})) {
                $this->invalidMethodHandler();
                return;
            }
            $methodDictionary = $this->{strtolower($this->request->requestMethod)};
            $formatedRoute = $this->formatRoute($this->request->pathInfo);
            $method = $methodDictionary[$formatedRoute]??null;
            if(is_null($method)) {
                $this->defaultRequestHandler();
                return;
            }

            echo call_user_func_array($method, array($this->request));
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    function __destruct()
    {
        $this->resolve();
    }
}
