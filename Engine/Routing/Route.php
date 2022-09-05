<?php


namespace app\Machine\Engine\Routing;


use app\Machine\Engine\Support\Session;
use app\Machine\Request;
use app\Machine\Response;
use app\Machine\Router;

class Route
{

    public static Router $router;
    public static Request $request;
    public static Response $response;
    public static Session $session;

    public static Route $route;

    /**
     *
     */
    public function __construct()
    {
        self::$route       = $this;

        self::$request      = new Request();
        self::$response     = new Response();
        self::$session     = new Session();
        self::$router       = new Router(self::$request, self::$response);

    }

    /**
     * @param $path
     * @param $callback
     */
    public static function get($path, $callback)
    {
        self::$router->get($path, $callback);
    }

    /**
     * @param $path
     * @param $callback
     */
    public static function post($path, $callback)
    {
        self::$router->post($path, $callback);
    }

    /**
     * @return Router
     */
    public function Web(): Router
    {
        return self::$router;
    }

}