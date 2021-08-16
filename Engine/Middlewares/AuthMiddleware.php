<?php


namespace app\Machine\Engine\Middlewares;


use app\Machine\App;

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
            if (empty($this->actions) || in_array(App::$app->controller->action, $this->actions)){
                redirect('login');
            }
        }
    }
}