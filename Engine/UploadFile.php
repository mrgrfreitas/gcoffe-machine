<?php


namespace app\Machine\Engine;


use app\Machine\Engine\Helpers\Checker;
use app\Machine\Engine\Helpers\Upload;

class UploadFile
{

    protected $width;
    protected $height;


    public static function storage($path, $file, $size, $name = null)
    {
        if(Checker::mimeTypeForImage($file['type'])){
            return (new static())->newImageUpload($path, $file, $size, $name);

        }elseif (Checker::mimeTypeForMedia($file['type'])){
            return (new static())->newImageMedia($path, $file, $name);

        }elseif (Checker::mimeTypeForArchives($file['type'])){
            return (new static())->newImageArchive($path, $file, $name);

        }else{
            echo ('File not support..');exit();
        }

    }

    public function setSize($file){
        $this->width = getimagesize($file['tmp_name'])[0];
        $this->height = getimagesize($file['tmp_name'])[1];

    }

    /**
     * @param $file
     * @param $path
     */
    public function newImageUpload($path, $file, $size, $name)
    {
        if (empty($size)){
            $this->setSize($file);
        }else{
            $this->width = $size['w'];
            $this->height = $size['h'];
        }

        $name = $name ?? md5(date('Ymdhis'));

        $upload = new Upload();
        $upload->Image($file, $name, $path, $this->width, $this->height);

        return $upload->getResult();
    }

    /**
     * @param $file
     * @param $path
     */
    public function newImageMedia($path, $file)
    {
        return (new Upload())->Media();
    }

    /**
     * @param $file
     * @param $path
     */
    public function newImageArchive($path, $file)
    {
        return (new Upload())->File();
    }

}