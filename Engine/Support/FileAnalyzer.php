<?php
declare(strict_types=1);


namespace app\Machine\Engine\Support;


use app\Machine\Engine\UploadFile;
use app\Machine\Request;
use app\Models\Gallery;

/**
 *
 */
trait FileAnalyzer
{
    protected $Archives;
    public $filaName;
    protected array $file_size = [];

    /**
     * @param $path
     * @return string
     */
    public function getPathFiles($path): string
    {
        if(is_null($path)){
            return 'gallery';
        }else{
            return $path;
        }

    }


    /**
     * @param $album
     * @return mixed
     */
    public function createAlbumName($album): mixed
    {
        if(is_null($album)){
            return 'Album-'.date('d-m-y').(substr(md5((string)time()), 0, 5));
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
    public function file($id = null)
    {
        // When update
        if ($id !== null){
            $_SESSION['temp_id'] = $id;
        }

        $this->Archives = $this->separateFiles((new Request())->data()['filesystem']);

        return $this;
    }

//    public function files()
//    {
//        $this->separateFiles((new Request())->data()['filesystem']);
//        return $this;
//    }

    /**
     * @param string $path
     * @return STRING|void
     */
    public function upload(string $path)
    {

        $path = $this->getPath($path);

        if (!is_null($this->file()->Archives)){
            $fileName = md5(date('Ymdhis'));
            $this->filaName = UploadFile::storage($path, $this->file()->Archives, $this->sizing()->file_size, $fileName);
            return $this->filaName;
        }else{
            //GWError("File can't be null, please select a file", GW_DANGER);
        }

    }


    /**
     * @param $files
     * @return mixed|null
     */
    public function separateFiles($files)
    {

        $simpleFile = null;
        if (is_array($files) && !empty($files)) {

            $countArrayFiles = count($files);

            // Simple File
            if ($countArrayFiles == 1 && array_key_first($files) !== 'gallery_images'){

                $simpleFile = array_shift($files);
            }

            // Only Gallery
            if ($countArrayFiles == 1 && array_key_first($files) === 'gallery_images'){

                $simpleFile = null;
                $this->multipleFiles($files);
            }

            // Simple file and gallery
            if ($countArrayFiles == 2){

                $simpleFile = array_shift($files);
                $this->multipleFiles($files);
            }

        }


        return $simpleFile;
    }

    /**
     * @param null $width
     * @param null $height
     * @return $this
     */
    public function sizing($width = null, $height = null)
    {
        $this->file_size = [
            'w' => !is_null($width) ? $width : getimagesize($this->file()->Archives['tmp_name'])[0],
            'h' => !is_null($height) ? $height : getimagesize($this->file()->Archives['tmp_name'])[1]
        ];

        return $this;
    }


    /**
     * @param $body
     * @return mixed
     */
    public function attrFileRequest($body)
    {
        if(isset($body['filesystem'])){
            $attrFile = array_key_first($body['filesystem']);
            $body[$attrFile] = $this->filaName;

            if (isset($_SESSION['temp_id'])){
                $body['temp_image'] = findWhere($this->getTable(), $this->primaryKey, $_SESSION['temp_id'], $attrFile);
            }

            unset($body['filesystem']);

            if($body[$attrFile] === null){
                unset($body[$attrFile]);
            }
        }

        return $body;
    }

    /**
     * @param $files
     * @param $id
     * @param $path
     */
    public function storeFiles($files, $id, $path)
    {

        $FILES = array();
        $FilesCount = count($files['tmp_name']);
        $FileKeys = array_keys($files);

        for ($file = 0; $file < $FilesCount; $file++):
            foreach ($FileKeys as $Keys):
                $FILES[$file][$Keys] = $files[$Keys][$file];
            endforeach;
        endfor;

        $this->uploadAndSave($FILES, $id, $path);

    }

    /**
     * @param array $files
     * @return void|null
     */
    private function multipleFiles(array $files)
    {
        if (!empty($files)){
            session()->set('multiple_fies', $files);
        }else{
            return null;
        }
    }

    /**
     * @param array $FILES
     * @param $id
     * @param $path
     * @return bool
     */
    private function uploadAndSave(array $FILES, $id, $path): bool
    {
        $i = 1;

        foreach ($FILES as $ImageUpload):
            $pathFiles      = $this->getPathFiles($path);
            //$albumFiles     = $this->createAlbumName($album);
            $fileName       = 'gallery-'. $i .(substr(md5((string)time()), 0, 15));

            $width  = getimagesize($ImageUpload['tmp_name'])[0];
            $height = getimagesize($ImageUpload['tmp_name'])[1];

            $uploaded = UploadFile::storage($pathFiles, $ImageUpload, $this->sizing($width, $height)->file_size, $fileName);

            if ($uploaded !== null):
                $data = [
                    'album_id' => $id,
                    'image_name' => $uploaded
                ];

                Gallery::saveGallery($data);
            endif;
            $i++;
        endforeach;

        return true;
    }

}