<?php
namespace app\Machine;

use app\Controllers\Controller;
use app\Machine\Exception\ForbiddenException;
use app\Machine\Exception\NotFoundException;

class Router
{
    public Request $request;
    public Response $response;

    protected array $routes = [];

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }


    public function get(string $path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * @throws NotFoundException
     */
    public function loading()
    {
        $path   = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;

        if($callback === false){
            throw new NotFoundException();
            //return view('errors/404');
        }


        if(is_string($callback)){
            return view($callback);
        }

        if(is_array($callback)){
            /** @var Controller $controller */

            $controller = new $callback[0]();
            App::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;

            foreach ($controller->getMiddleware() as $middleware){
                $middleware->execute();
            }

        }


        return call_user_func($callback, $this->request);

    }

}