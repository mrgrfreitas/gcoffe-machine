<?php
namespace app\Machine;

use app\Controllers\Controller;
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

    public function getCallback()
    {
        $path   = $this->request->getPath();
        $method = $this->request->method();

        // Trim slashes
        $path = trim($path, '/');

        // Get all routes for current request method
        $routes = $this->routes[$method] ?? [];

        // Start iterating registered routes
        foreach ($routes as $route => $callback){

            // Trim slashes
            $route = trim($route, '/');
            $routesNames = [];

            if (!$route){
                continue;
            }


            // Find all route names from route and save in $routeNames
            if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)){
                $routesNames = $matches[1];
            }

            // Convert route name into Regex Pattern
            $routeRegex = "@^" . preg_replace_callback('/\{(\w+)(:[^}]+)?}/', fn($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)' , $route) . "$@";

            if (preg_match_all($routeRegex, $path, $valueMatches)){
                $values = [];

                for ($i = 1; $i < count($valueMatches); $i++){
                    $values[] = $valueMatches[$i][0];
                }

                $routeParams = array_combine($routesNames, $values);

                $this->request->setRouteParams($routeParams);

                return $callback;
            }

        }

        return  false;
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
            $callback = $this->getCallback();

            if ($callback === false){
                throw new NotFoundException();
                //return view('errors/404');
            }
        }


        if(is_string($callback)){
            return view($callback);
        }

        if(is_array($callback)){
            /** @var Controller $controller */

            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;

            foreach ($controller->getMiddleware() as $middleware){
                $middleware->execute();
            }

        }

        return call_user_func($callback, $this->request);

    }

}