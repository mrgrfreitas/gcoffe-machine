<?php

namespace app\Machine\Engine\Auth;

use app\Controllers\Auth\authController;
use app\Machine\Engine\Support\Session;
use app\Machine\Request;
use app\Machine\Response;
use app\Machine\Router;

class Auth
{
    public static Router $router;
    public static Request $request;
    public static Response $response;

    public static Auth $auth;

    /**
     *
     */
    public function __construct()
    {
        self::$auth         = $this;

        self::$request      = new Request();
        self::$response     = new Response();
        self::$router       = new Router(self::$request, self::$response);
    }

    public static function Login()
    {
        if (self::$request->isGet()){
            self::$router->get('/login', [authController::class, 'login']);
        }

        if (self::$request->isPost()){
            self::$router->post('/login', [authController::class, 'login']);
        }

    }

    public static function Register()
    {
        if (self::$request->isGet()){
            self::$router->get('/register', [authController::class, 'register']);
        }

        if (self::$request->isPost()){
            self::$router->post('/register', [authController::class, 'register']);
        }

    }

    public static function Unlock()
    {
        if (self::$request->isGet()){
            self::$router->get('/unlock', [authController::class, 'unlock']);
        }

        if (self::$request->isPost()){
            self::$router->post('/unlock', [authController::class, 'unlock']);
        }

    }

    public static function Logout()
    {
        self::$router->get('/logout', [authController::class, 'logout']);
    }

    /**
     * Groups all auth routes, (e.g. Login, Register, Logout and Unlock routes)
     */
    public static function Routes()
    {
        self::Login();
        self::Register();
        self::Logout();
        self::Unlock();
    }

    /**
     * @return Router
     */
    public function Route(): Router
    {
        return self::$router;
    }

    /**
     * @return false|mixed
     */
    public function userOnSession()
    {
        $userId = Session::$session->get('user');
        if($userId){
            return Session::findUser($userId);
        }else{
            return [];
        }
    }
}