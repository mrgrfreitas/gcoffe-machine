<?php


namespace app\Machine;

use app\Machine\Engine\Support\FileAnalyzer;
use app\Machine\Engine\UploadFile;

/**
 * Class Request
 * class responsible for every request made in the system
 *
 * @package app\Machine
 * @author Geraldo Freitas
 */
class Request
{
//    protected $File;
//    protected array $file_size = [];
    protected array $get = [];
    private array $routeParams = [];
    private array $fieldsData = [];


    public function __construct()
    {
        // Check if exist the GET DATA... we can use this when we work with POST and GET DATA in the same PAGE...
        $this->get = $this->checkForGetData();
    }


    public function getPath()
    {
        //posicao da pasta
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        if($position === false){
            return $path;
        }
        return substr($path, 0, $position);

    }

    /**
     * @param $params
     * @return $this
     */
    public function setRouteParams($params): static
    {
        $this->routeParams = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getParam($key): mixed
    {
        return $this->getRouteParams()[$key];
    }

    /*
     * get the method request... e.g.(get OR post)
     */
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() == 'get';
    }
    public function isPost()
    {
        return $this->method() == 'post';
    }

    /**
     * Determine if the request is the result of an AJAX call.
     *
     * @return bool
     */
    public function ajax(): bool
    {
        return $this->isXmlHttpRequest();
    }

    public function getData()
    {
        return $this->get;
    }

    public function input($key)
    {
        return $this->getData()[$key];
    }


    /**
     * @return mixed|null
     */
    public function getId()
    {
        $id = $this->getData();
        return array_shift($id);
    }

    /**
     * @return mixed|null
     */
    public function getSlug()
    {
        $slug = $this->getData();
        return array_shift($slug);
    }

    /**
     * This function return the arrays values of some forms or url INPUT_GET or INPUT_POST data...
     */
    public function data(array $fields = null)
    {

        /**
         * Validate if is get method
         */
        if ($this->method() === "get"){

            if (!empty($fields)){
                foreach ($fields as $key => $value){

                    // WHAT THIS WILL DO: it is look to a SUPER_GLOBAL GET using the constant INPUT_GET,
                    // it is had looked in following $key, take the value remove in the value some character and put it in body
                    $this->fieldsData[$key] = $value;
                }
            }

            foreach ($_GET as $key => $value){

                // WHAT THIS WILL DO: it is look to a SUPER_GLOBAL GET using the constant INPUT_GET,
                // it is had looked in following $key, take the value remove in the value some character and put it in body
                $this->fieldsData[$key] = filter_input(INPUT_GET, $key, FILTER_DEFAULT);
            }
        }

        /**
         * validate if is post method
         */
        if ($this->method() === "post"){
            $this->checkPostMethod($fields);
        }

        /**
         * validate if is ajax
         */
        if ($this->ajax()){
            $this->checkPostMethod($fields);
        }

        return $this->fieldsData;
    }

    /**
     * @param $fields
     * @return void
     */
    private function checkPostMethod($fields): void
    {
        if (!empty($fields)){
            foreach ($fields as $key => $value){

                // WHAT THIS WILL DO: it is look to a SUPER_GLOBAL GET using the constant INPUT_GET,
                // it is had looked in following $key, take the value remove in the value some character and put it in body
                $this->fieldsData[$key] = $value;
            }
        }

        foreach ($_POST as $key => $value){

            if(is_array($value)){
                $this->fieldsData[$key] = implode(',', $value);
            }else{
                // WHAT THIS WILL DO: it is look to a SUPER_GLOBAL POST using the constant INPUT_POST,
                // it is had looked in following $key, take the value remove in the value some character and put it in body
                $this->fieldsData[$key] = filter_input(INPUT_POST, $key, FILTER_DEFAULT);
            }

        }

        foreach ($_FILES as $key => $value){

            if($value['tmp_name'] === '' ){
                unset($_FILES[$key]);
            }

            $this->fieldsData['filesystem'] = $_FILES;
        }

    }

    /**
     * @return array
     */
    private function checkForGetData(): array
    {
        if(!empty($_GET)){
            return $_GET;
        }else{
            return [];
        }
    }

    /**
     * @return bool
     */
    private function isXmlHttpRequest(): bool
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            return true;
        }else{
            return false;
        }
    }

}