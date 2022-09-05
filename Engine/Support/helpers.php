<?php

use app\Controllers\Controller;
use app\Machine\Application;
use app\Machine\Engine\Datatable\Datatable;
use app\Machine\Engine\Gears\Repositories;
use app\Machine\Engine\Support\DateFormatter;
use app\Machine\Engine\Support\Env;
use app\Machine\Engine\Support\Session;
use app\Machine\Engine\Valve\Query\BuildsQueries;
use app\Machine\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;


if (! function_exists('_env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function _env($key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (! function_exists('findOne')) {
    /**
     * Build query to find a specific value.
     * @param $var
     * @return mixed
     */
    function findOne($var)
    {
        $var = explode('.', $var);
        return BuildsQueries::one($var[0], $var[1]);
    }
}

if (! function_exists('findWhere')) {
    /**
     *  Build query to find a specific value.
     *
     *  findWhere('model, reference, value, 'column').
     * @param $model
     * @param $reference
     * @param $value
     * @param null $column
     * @return mixed
     */
    function findWhere($model, $reference, $value, $column = null)
    {
        //$var = explode('.', $var);var_dump($var);
        return BuildsQueries::where($model, $reference, $value, $column);
    }
}

if (! function_exists('previous')) {
    /**
     * @param $model
     * @param $id
     * @return mixed
     */
    function previous($model, $id)
    {
        $model = explode('.', $model);
        return BuildsQueries::previous($model[0], $model[1], $id);
    }
}

if (! function_exists('nextTo')) {
    /**
     * @param $model
     * @param $id
     * @return mixed
     */
    function nextTo($model, $id)
    {
        $model = explode('.', $model);
        return BuildsQueries::nextTo($model[0], $model[1], $id);
    }
}

if (! function_exists('findGallery')) {
    /**
     * Build query to find a specific value.
     * @param $id
     * @return mixed
     */
    function findGallery($id)
    {
        return BuildsQueries::gallery($id);
    }
}

/**
 * @param $array
 * @param $id
 * @return string
 */
function in_array_exploded($array, $id): string
{
    $array = explode(',', $array);
    if(in_array($id, $array)){
        return 'block';
    }else{
        return 'none';
    }
}

/**
 * @param $var
 * @return string
 */
function money($var)
{
    return number_format($var, 2, ',', '.');
}

/**
 * @param $value
 * @param int $limit
 * @param string $end
 * @return string
 */
function strLimit($value, $limit = 100, $end = '...')
{
    return Str::limit($value, $limit, $end);
}

function timeAgo($time)
{
    DateFormatter::DatetimeAgo($time);
}


/**
 * @param $values
 * @return array
 */
function getKeywords($values)
{
    if(is_array($values)){
        foreach ($values as $array){
            $keywords[] = $array['metaKeywords'];
            $joinKeywords = implode('', $keywords);
        }
    }else{

        $joinKeywords = $values;
    }

    $removeSpace = array_map('trim', (array_filter(explode("#", $joinKeywords))));

    /** remove Duplicate Keyword */
    return array_unique($removeSpace);
}


/**
 * @param $url_api
 * @param $class_api
 * @return mixed
 */
function get_api_response($url_api, $class_api, $param_api = null){

    $url = $_ENV[$url_api];
    if (!is_null($param_api)){
        $param_api = '?s='. $param_api;
    }

    $response = file_get_contents($url.$class_api.$param_api);

    return json_decode($response, 1);
}

/**
 * @return string
 */
function seoTitle(): string
{
    return Application::$app->seo->getTitle();
}

/**
 * @return string
 */
function seoContent(): string
{
    return strLimit(Application::$app->seo->getContent(), 200);
}

/**
 * @return string
 */
function seoLink(): string
{
    return Application::$app->seo->getLink();
}

/**
 * @return string
 */
function seoImage(): string
{
    return Application::$app->seo->getImage();
}

/**
 * @param $path
 * @param $image
 * @return string
 */
function getImageUrl($path, $image): string
{
    return storage_url($path . '/' . $image);
}

/**
 * @param $url
 * @param $param
 * @return string
 */
function getLinkUrl($url, $param): string
{
    return route($url . '?s=' . $param);
}


/**
 * Return true when the user isn´t logger
 * @return bool
 */
function guest()
{
    return !(Application::$app->user);
}

/**
 * @return false|mixed
 * return a display html session of paginator
 */
function linkPage()
{
    return Session::$session->get('linkPage');
}


/**
 * @return Datatable
 */
function datatables()
{
    return new Datatable();
}










##################################################################################################################

/**
 * @param $url
 * @return void
 */
function redirectTo($url): void
{
    Response::redirect($url);
}

/**
 * @return Response
 */
function response(): Response
{
    return (new Response());
}


function app($value)
{
    return Application::$app->$value;
}

function userOnSession()
{
    return Session::$session->get('userOnSession');
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
function diffDate($date1, $date2): int
{
    return DateFormatter::diffDate($date1, $date2);
}

/**
 * Increase days from the current date or a user-defined date
 * @param $days
 * @param $date
 * @return string
 */
function IncreaseDaysToDate($days, $date = null): string
{
    return DateFormatter::IncreaseDaysToDate($days, $date);
}

/**
 * @param $date1
 * @param $date2
 * @return array
 */
function allDaysPeriod($date1, $date2): array
{
    return DateFormatter::all_days_period($date1, $date2);
}

/**
 * @param $date
 * @return string
 */
function dayMonthYear($date): string
{
    return DateFormatter::dayMonthYear($date);
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
 * @param $message
 * @return void
 */
function logJsonMessage($message): void
{
    echo json_encode($message);
}

/**
 * @param $key
 * @param $message
 */
function logMessage($key, $message)
{
    Session::$session->setFlash($key, $message);
}

function logMessageDisplay()
{
    if(Session::$session->getFlash('primary')):
        logMessageLayout('primary');
    endif;

    if(Session::$session->getFlash('success')):
        logMessageLayout('success');
    endif;

    if(Session::$session->getFlash('danger')):
        logMessageLayout('danger');
    endif;

    if(Session::$session->getFlash('warning')):
        logMessageLayout('warning');
    endif;

    if(Session::$session->getFlash('info')):
        logMessageLayout('info');
    endif;
}

function logMessageLayout($key)
{
    echo "<div class='alert alert-$key alert-dismissible fade show alert-position'  role='alert'>";
        echo Session::$session->getFlash($key);
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
    echo "</div>";
}

/**
 * @return Session
 */
function session(): Session
{
    return Session::$session;
}

/**
 * @param $dir
 * @return string
 */
function store($dir): string
{
    return FILE['disks']['public']['url'] . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $dir;
}

function logger($error, $key = null, $message = null)
{
    $date = "*********************" . Date::now()->toDateTimeString() . "*********************";
    file_put_contents(config('logging.emergency'), $date, FILE_APPEND);
    file_put_contents(config('logging.emergency'), $error, FILE_APPEND);

    if($key != null && $message != null){
        logMessage($key, $message);
    }
}

if (! function_exists('asset')) {
    /**
     * @param $path
     * @param null $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        return config('app.asset_url') . DIRECTORY_SEPARATOR . $path;
    }
}

if (! function_exists('url')) {

    /**
     * @param null $path
     * @param array $parameters
     * @param null $secure
     * @return string
     */
    function url($path = null, $parameters = [], $secure = null)
    {
        return config('app.asset_url') . DIRECTORY_SEPARATOR . $path;
    }
}

//if (! function_exists('route')) {
//    /**
//     * Generate the URL to a named route.
//     *
//     * @param  array|string  $name
//     * @param  mixed  $parameters
//     * @param  bool  $absolute
//     * @return string
//     */
//    function route($name, $parameters = [], $absolute = true)
//    {
//            if ($name == '/'){
//                return config('app.url');
//            }else{
//                return config('app.url') ."/". $name;
//            }
//    }
//}

if (! function_exists('config')) {
    /**
     * @param null $key
     * @param null $default
     * @return void|null
     */
    function config($key = null, $default = null)
    {
        return (new Repositories())->get($key, $default);
    }
}

if (! function_exists('database_path')) {
    /**
     * Get the database path.
     *
     * @param  string  $path
     * @return string
     */
    function database_path($path = '')
    {
        return rootDir('database') . DIRECTORY_SEPARATOR . $path;
    }
}

if (! function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param  string  $path
     * @return string
     */
    function storage_path($path = '')
    {
        return rootDir('storage') . DIRECTORY_SEPARATOR . $path;
    }
}

if (! function_exists('resource_path')) {
    /**
     * Get the path to the resources folder.
     *
     * @param  string  $path
     * @return string
     */
    function resource_path($path = '')
    {
        return rootDir('resources') . DIRECTORY_SEPARATOR . $path;
    }

}
