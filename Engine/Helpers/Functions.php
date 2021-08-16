<?php


use app\Controllers\Controller;
use app\Machine\App;
use app\Machine\Engine\Helpers\DateFormatter;
use app\Machine\Engine\Helpers\Session;
use app\Machine\Response;
use Illuminate\Support\Facades\Date;

function redirect($url)
{
    Response::redirectTo($url);
}
function app($value)
{
    return App::$app->$value;
}

function guest()
{
    return !(App::$app->user);
}

/**
 * Get the evaluated view contents for the given view.
 * @param $view
 * @param array $data
 * @return string
 */
function view($view, array $data = [])
{
    return Controller::make($view, $data);
}

/**
 * @param $date1
 * @param $date2
 * @return int
 */
function diffDate($date1, $date2)
{
    return DateFormatter::diffDate($date1, $date2);
}

/**
 * @param $url
 * @return false|string
 */
function decryptGetUlr($url)
{

    $decrypt = explode('=', $url);
    if(isset($decrypt[1])){
        return base64_decode($decrypt[1]);
    }else{
        return base64_decode($decrypt[0]);
    }

}

function decryptUlrToArray($url)
{

    $decrypt = explode('=', $url);
    if(isset($decrypt[1])){
        $array = base64_decode($decrypt[1]);
        return explode('=', $array);
    }else{
        $array = base64_decode($decrypt[0]);
        return explode('=', $array);
    }

}

/**
 * @param $key
 * @param $message
 */
function logMessage($key, $message)
{
    return App::$app->session->setFlash($key, $message);
}

function logMessageDisplay()
{
    if(\app\Machine\App::$app->session->getFlash('primary')):
        logMessageLayout('primary');
    endif;

    if(\app\Machine\App::$app->session->getFlash('success')):
        logMessageLayout('success');
    endif;

    if(\app\Machine\App::$app->session->getFlash('danger')):
        logMessageLayout('danger');
    endif;

    if(\app\Machine\App::$app->session->getFlash('warning')):
        logMessageLayout('warning');
    endif;

    if(\app\Machine\App::$app->session->getFlash('info')):
        logMessageLayout('info');
    endif;
}

function logMessageLayout($key)
{
    echo "<div class='alert alert-$key alert-dismissible fade show alert-position'  role='alert'>";
        echo \app\Machine\App::$app->session->getFlash($key);
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
    echo "</div>";
}

/**
 * @return Session
 */
function session(): Session
{
    return App::$app->session;
}

function store($dir)
{
    $file = FILE['disks']['public']['url'] . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $dir;
    return $file;
}

function logger($error, $key = null, $message = null)
{
    $date = "*********************" . Date::now()->toDateTimeString() . "*********************";
    file_put_contents(logDir . '/gcoffee.log', $date, FILE_APPEND);
    file_put_contents(logDir . '/gcoffee.log', $error, FILE_APPEND);

    if($key != null && $message != null){
        logMessage($key, $message);
    }
}
