<?php


namespace app\Machine;


class Response
{

    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public static function redirect($url){

        $exp = explode('/', $url);
        $filter = array_filter($exp);
        $url = implode('/', $filter);

        header('Location: ' . $_ENV['APP_URL'] .'/'.$url);
    }

    public function json($data)
    {
        return json_encode($data);
    }

}