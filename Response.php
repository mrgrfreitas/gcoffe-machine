<?php


namespace app\Machine;


class Response
{

    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public static function redirectTo($url){
        header('Location: ' . $_ENV['APP_URL'] .'/'.$url);
    }

}