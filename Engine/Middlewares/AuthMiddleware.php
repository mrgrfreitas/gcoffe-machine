<?php


namespace app\Machine\Engine\Middlewares;


use app\Machine\Application;
use app\Machine\Engine\Support\Session;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions = [];

    /**
     * AuthMiddleware constructor.
     * @param array $actions
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (guest()){
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)){
                redirectTo('login');
            }
        }else{
            Session::$session->set('loginTimeSession', time());
        }

    }
}