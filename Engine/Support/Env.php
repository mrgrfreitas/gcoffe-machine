<?php

namespace app\Machine\Engine\Support;
use Dotenv\Dotenv;


class Env
{

    public static function get(string $key, $default = null)
    {
        $DIR = str_replace('\app\Machine\Engine', '', dirname(__DIR__));

        $dotEnv = Dotenv::createMutable($DIR);
        $dotEnv->load();

        return $_ENV[$key] ?? false;

    }
}