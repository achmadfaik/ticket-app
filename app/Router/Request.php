<?php

namespace App\Router;

class Request
{
    function __construct()
    {
        $this->bootstrapSelf();
    }

    private function bootstrapSelf()
    {
        foreach($_SERVER as $key => $value) {
            $this->{$this->toCamelCase($key)} = $value;
        }
    }

    private function toCamelCase($string)
    {
        $result = strtolower($string);

        preg_match_all('/_[a-z]/', $result, $matches);

        foreach($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }

        return $result;
    }

    public function getBody()
    {
        if($this->requestMethod === "GET") {
            return $_GET;
        }

        if ($this->requestMethod === "POST") {

            $body = array();
            $data = $_POST;
            if ($this->contentType === "application/json") $data = json_decode(file_get_contents('php://input'), true);
            foreach($data as $key => $value) {
                $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
            }

            return $body;
        }
    }
}
