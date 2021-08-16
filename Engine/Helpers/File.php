<?php


namespace app\Machine\Engine\Helpers;


use app\Machine\Engine\UploadFile;
use app\Machine\Request;
use app\Models\Gallery;

trait File
{
    protected $File;
    public $filaName;
    protected array $file_size = [];

    /**
     * @param $path
     * @return string
     */
    public function getPathFiles($path)
    {
        if(is_null($path)){
            return 'gallery';
        }else{
            return $path;
        }

    }

    public function getAlbumFiles($album)
    {
        if(is_null($album)){
            return 'Album-'.date('d-m-y').(substr(md5(time()), 0, 5));
        }else{
            return $album;
        }

    }

    /**
     * @param $path
     * @return string
     */
    public function getPath($path): string
    {
        if ($path !== ''){
            return $path;
        }else{
            return 'images';
        }

    }

    /**
     * @return $this
     */
    public function file(): File
    {
        $this->File = $this->separateFiles((new Request())->data()['filesystem']);
        return $this;
    }

    /**
     * @param string $path
     * @return STRING|void
     */
    public function upload(string $path)
    {
        $path = $this->getPath($path);

        if (!is_null($this->file()->File)){
            $fileName = md5(date('Ymdhis'));
            $this->filaName = UploadFile::storage($path, $this->file()->File, $this->sizing()->file_size, $fileName);
            return $this->filaName;
        }else{
            //GWError("File can't be null, please select a file", GW_DANGER);
        }

    }


    public function separateFiles($files)
    {
        if (is_array($files) && !empty($files)){
            if (count($files) > 1){
                $first = array_key_first($files);
                $articleFile = $files[$first];

                unset($files[$first]);

                $this->multipleFiles($files);

                return $articleFile;
            }
        }

    }

    public function sizing($width = null, $height = null): File
    {
        $this->file_size = [
            'w' => !is_null($width) ? $width : getimagesize($this->file()->File['tmp_name'])[0],
            'h' => !is_null($height) ? $height : getimagesize($this->file()->File['tmp_name'])[1]
        ];

        return $this;
    }


    public function attrFileRequest($body)
    {
        if(isset($body['filesystem'])){
            $attrFile = array_key_first($body['filesystem']);
            $body[$attrFile] = $this->filaName;
            unset($body['filesystem']);
        }
        if($body[$attrFile] !== null){
            unset($body[$attrFile]);
        }
        return $body;
    }

    /**
     * @param $files
     * @param $id
     * @param $path
     * @param $album
     */
    public function storeFiles($files, $id, $path, $album)
    {

        $Files = $files[array_key_first($files)];
        $image = array();
        $FilesCount = count($Files['tmp_name']);
        $FileKeys = array_keys($Files);

        for ($file = 0; $file < $FilesCount; $file++):
            foreach ($FileKeys as $Keys):
                $image[$file][$Keys] = $Files[$Keys][$file];
            endforeach;
        endfor;

        $this->uploadAndSave($image, $id, $path, $album);

    }

    private function multipleFiles(array $files)
    {
        if (!empty($files)){
            session()->set('multiple_fies', $files);
        }else{
            return null;
        }
    }

    private function uploadAndSave(array $image, $id, $path, $album)
    {
        $i = 1;
        foreach ($image as $ImageUpload):
            $pathFiles      = $this->getPathFiles($path);
            $albumFiles     = $this->getAlbumFiles($album);
            $fileName       = "gallery-" .(substr(md5(time() + $i), 0, 15));

            $uploaded = UploadFile::storage($pathFiles, $ImageUpload, $this->sizing()->file_size, $fileName);

            if ($uploaded !== null):
                $data = [
                    'album_id' => $id,
                    "albumName" => $albumFiles,
                    "image_name" => $uploaded
                ];

                Gallery::saveGallery($data);
            endif;
            $i++;
        endforeach;

        return true;
    }

}