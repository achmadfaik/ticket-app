<?php


namespace App\Router;


class Validate
{
    public array $message = [];

    public function rules($data, $rules) {
        foreach($rules as $key => $value) {
            foreach($value as $rule => $error) {
                switch($rule) {
                    case 'required':
                        if(empty($data[$key]) == $key) {
                            $this->message[$key] = $error;
                        }
                        break;

                    case 'numeric':
                        if(!is_numeric($data[$key]) == $key) {
                            $this->message[$key] = $error;
                        }
                        break;
                }
            }

        }
    }

    public function getMessages() {
        return $this->message;
    }
}
