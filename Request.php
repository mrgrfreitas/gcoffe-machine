<?php


namespace app\Machine;

use app\Machine\Engine\Helpers\File;
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


    public function __construct()
    {
        // Check if have the GET DATA... we can use this when we work with POST and GET DATA in the same PAGE...
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

    public function getData()
    {
        return $this->get;
    }

    /**
     * This function return the arrays values of some forms or url INPUT_GET or INPUT_POST data...
     */
    public function data(array $fields = null)
    {
        $body = [];

        if ($this->method() === "get"){

            if (!empty($fields)){
                foreach ($fields as $key => $value){

                    // WHAT THIS WILL DO: it is have look to a SUPER_GLOBAL GET using the constant INPUT_GET,
                    // it is have look in following $key, take the value remove in the value some character and put it in body
                    $body[$key] = $value;
                }
            }

            foreach ($_GET as $key => $value){

                // WHAT THIS WILL DO: it is have look to a SUPER_GLOBAL GET using the constant INPUT_GET,
                // it is have look in following $key, take the value remove in the value some character and put it in body
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->method() === "post"){

            if (!empty($fields)){
                foreach ($fields as $key => $value){

                    // WHAT THIS WILL DO: it is have look to a SUPER_GLOBAL GET using the constant INPUT_GET,
                    // it is have look in following $key, take the value remove in the value some character and put it in body
                    $body[$key] = $value;
                }
            }

            foreach ($_POST as $key => $value){

                if(is_array($value)){
                    $body[$key] = implode(',', $value);
                }else{
                    // WHAT THIS WILL DO: it is have look to a SUPER_GLOBAL POST using the constant INPUT_POST,
                    // it is have look in following $key, take the value remove in the value some character and put it in body
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }

            }

            foreach ($_FILES as $key => $value){

                if($value['tmp_name'] === '' ){
                    unset($_FILES[$key]);
                }

                $body['filesystem'] = $_FILES;
            }
        }
        return $body;
    }

    /**
     * @param array|string $key
     * @return $this
     */
//    public function file($file = '')
//    {
//
//        //$file = File::separateFiles($key);
//
//        if (is_array($file)){
//
//            $this->File = $file;
//        } elseif ($file == ''){
//
//            foreach ($_FILES as $file => $value){
//                $this->File = $value;
//            }
//
//        }else{
//
//            $this->File = $_FILES[$file];
//        }
//        return $this;
//    }

//    public function size($width = null, $height = null)
//    {
//        $this->file_size = [
//          'w' => !is_null($width) ? $width : getimagesize($this->file()->File['tmp_name'])[0],
//          'h' => !is_null($height) ? $height : getimagesize($this->file()->File['tmp_name'])[1]
//        ];
//
//        return $this;
//    }

    /**
     * @param $w
     * @param $h
     * @param string $path
     * @return STRING|void
     */
//    public function thumbnail($w, $h, $path = '')
//    {
//        if (!is_null($this->file()->File)){
//
//            $this->file_size = [
//                'w' => $w,
//                'h' => $h
//            ];
//
//            return UploadFile::storage($path, $this->File, $this->file_size);
//
//        }else{
//            GWError("File can't be null, please select a file", GW_DANGER);
//        }
//    }

    /**
     * @param string $path
     * @return STRING|void
     */
//    public function store($path = '')
//    {
//        if (!is_null($this->file()->File)){
//            return UploadFile::storage($path, $this->file()->File, $this->size()->file_size);
//        }else{
//            GWError("File can't be null, please select a file", GW_DANGER);
//        }
//
//    }

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

}