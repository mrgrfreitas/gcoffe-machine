<?php

namespace app\Machine\Engine\Support;


use app\Machine\Engine\Auth\Events\Verified;
use app\Machine\Request;

class Session
{

    use Verified;

    protected const  FLASH_KEY = 'flash_message';
    public static Session $session;

    public function __construct()
    {
        session_start();

        self::$session       = $this;

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMessages as $key => &$flashMessage){
            //Mark to be removed
            $flashMessage['remove'] = true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;

        //$this->session_timeout();
    }

    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function destroy($key)
    {
        unset($_SESSION[$key]);
    }

    public static function findUser($id)
    {
        return self::find($id);
    }

    public function login($findUser): bool
    {
        $pk = $findUser['id'];
        $pkValue = $pk;
        $this->set('user', $pkValue);
        $this->set('loginTimeSession', time());
        return true;
    }

    public function logout()
    {
        $this->destroy('user');
        $this->destroy('loginTimeSession');
    }

    /**
     * Break session when timeout
     */
    public function session_timeout()
    {
        if ($this->get('user') !== false){

            $session_realtime = time() - $this->get('loginTimeSession');
            $session_lifetime = (30 * 60);

            $request = new Request();
            $request_uri = $request->getPath();

            if ($session_realtime > $session_lifetime){

                $this->set('request_uri', $request_uri);
                Session::$session->set('userOnSession', app('user')['email']);
                Session::$session->logout();
                redirectTo('unlock');
            }

        }
    }

    protected function userEmailSession()
    {
        return $this->findUser($this->get('user'))['email'];
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMessages as $key => &$flashMessage){
            if($flashMessage['remove']){
                unset($flashMessages[$key]);
            }
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }


}