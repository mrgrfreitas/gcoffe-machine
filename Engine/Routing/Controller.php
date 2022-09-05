<?php


namespace app\Machine\Engine\Routing;


use app\Machine\Engine\Middlewares\BaseMiddleware;

abstract class Controller
{
    public string $option = '';
    /**
     * @var BaseMiddleware[]
     */
    protected array $middlewares = [];

    public function middleware(BaseMiddleware $middleware){
        $this->middlewares[] = $middleware;
    }

    public function getMiddleware()
    {
        return $this->middlewares;
    }

}