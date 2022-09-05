<?php


namespace app\Machine\Engine\Support;

use PDOException;

/**
 * Class Upload
 * This class performs image, file and media uploads on the system!
 * @package app\Machine\Engine\Cylinders\Helpers
 */
class Upload
{

    private $File;
    private $Name;

    /** IMAGE UPLOAD */
    private $Width;
    private $Heigth;
    private $Image;

    /** RESULTSET */
    private $Result;
    private $Error;

    /** DIR */
    private $Folder;
    private $folderDir;
    private static $storeDir;
    private static $siteDir;


    /**
     * Upload constructor.
     * Check and create the default upload directory on the system!
     * @param null $BaseDir
     */
    public function __construct($BaseDir = null)
    {
        if (!is_null(FILE['disks']['rootFolder']) && !is_null(FILE['disks']['siteFolder'])){
            self::$siteDir = str_replace(FILE['disks']['rootFolder'], FILE['disks']['siteFolder'], rootDir());
        }

        self::$storeDir = ((string)$BaseDir ? $BaseDir : FILE['disks']['public']['root']);
        if (!file_exists(self::$storeDir) && !is_dir(self::$storeDir)):
            mkdir(self::$storeDir, 0755);
        endif;
    }

    /**
     * @return mixed
     */
    public function getFolderDir()
    {
        return $this->folderDir;
    }

    /**
     * @param mixed $folderDir
     */
    public function setFolderDir($folderDir): void
    {
        $this->folderDir = $folderDir;
    }


    /**
     * @param array $Image
     * @param null $Name
     * @param null $Folder
     * @param null $Width
     * @param null $Height
     */
    public function Image(array $Image, $Name = null, $Folder = null, $Width = null, $Height = null)
    {
        $this->File = $Image;
        $this->Name = ((string)$Name ? $Name : md5(date('Ymdhis')));
        $this->Width = ((int)$Width ? $Width : 1024);
        $this->Heigth = ((int)$Height ? $Height : 1024);
        $this->Folder = ((string)$Folder ? $Folder : 'images');

        $this->CreateFolder($this->Folder);
        $this->setFileName();
        $this->UploadImage();
    }

    /**
     * <b>Enviar Arquivo:</b> Basta envelopar um $_FILES de um arquivo e caso queira um nome e um tamanho personalizado.
     * Caso não informe o tamanho será 2mb!
     * @param FILES $File = Enviar envelope de $_FILES (PDF ou DOCX)
     * @param STRING|null $Name = Nome do arquivo ( ou do artigo )
     * @param STRING|null $Folder = Pasta personalizada
     * @param STRING|null $MaxFileSize = Tamanho máximo do arquivo (2mb)
     */
    public function File(array $File, string $Name = null, string $Folder = null, string $MaxFileSize = null)
    {
        $this->File = $File;
        $this->Name = ((string)$Name ? $Name : substr($File['name'], 0, strrpos($File['name'], '.')));
        $this->Folder = ((string)$Folder ? $Folder : 'files');
        $MaxFileSize = ((int)$MaxFileSize ? $MaxFileSize : 2);

        $FileAccept = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf'
        ];

        if ($this->File['size'] > ($MaxFileSize * (1024 * 1024))):
            $this->Result = false;
            $this->Error = "Arquivo muito grande, tamanho máximo permitido de {$MaxFileSize}mb";
        elseif (!in_array($this->File['type'], $FileAccept)):
            $this->Result = false;
            $this->Error = 'Tipo de arquivo não suportado. Envie .PDF ou .DOCX!';
        else:
            $this->CreateFolder($this->Folder);
            $this->setFileName();
            $this->MoveFile();
        endif;
    }

    /**
     * <b>Enviar Mífia:</b> Basta envelopar um $_FILES de uma mídia e caso queira um nome e um tamanho personalizado.
     * Caso não informe o tamanho será 40mb!
     * @param FILES $Media = Enviar envelope de $_FILES (MP3 ou MP4)
     * @param STRING $Name = Nome do arquivo ( ou do artigo )
     * @param STRING $Folder = Pasta personalizada
     * @param STRING $MaxFileSize = Tamanho máximo do arquivo (40mb)
     */
    public function Media(array $Media, $Name = null, $Folder = null, $MaxFileSize = null)
    {
        $this->File = $Media;
        $this->Name = ((string)$Name ? $Name : substr($Media['name'], 0, strrpos($Media['name'], '.')));
        $this->Folder = ((string)$Folder ? $Folder : 'medias');
        $MaxFileSize = ((int)$MaxFileSize ? $MaxFileSize : 40);

        $FileAccept = [
            'audio/mp3',
            'video/mp4'
        ];

        if ($this->File['size'] > ($MaxFileSize * (1024 * 1024))):
            $this->Result = false;
            $this->Error = "Arquivo muito grande, tamanho máximo permitido de {$MaxFileSize}mb";
        elseif (!in_array($this->File['type'], $FileAccept)):
            $this->Result = false;
            $this->Error = 'Tipo de arquivo não suportado. Envie audio MP3 ou vídeo MP4!';
        else:
            $this->CreateFolder($this->Folder);
            $this->setFileName();
            $this->MoveFile();
        endif;
    }

    /**
     * <b>Verificar Upload:</b> Executando um getResult é possível verificar se o Upload foi executado ou não. Retorna
     * uma string com o caminho e nome do arquivo ou FALSE.
     * @return STRING  = Caminho e Nome do arquivo ou False
     */
    public function getResult()
    {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com um code, um title, um erro e um tipo.
     * @return ARRAY $Error = Array associatico com o erro
     */
    public function getError()
    {
        return $this->Error;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Verifica e cria o diretório base!
    private function CreateFolder($Folder)
    {
        $expFolder = explode('.', $Folder);

        if (isset($expFolder[0]) && $expFolder[0] == 'site'){

            $siteDir = self::$siteDir.'/storage/files/public/'.$expFolder[1];
            if (!file_exists($siteDir) && !is_dir($siteDir)):
                mkdir($siteDir, 0755, true);
            endif;

            $this->setFolderDir($siteDir);

        }else{

            if (!file_exists(self::$storeDir . DIRECTORY_SEPARATOR . $Folder) && !is_dir(self::$storeDir . DIRECTORY_SEPARATOR . $Folder)):
                mkdir(self::$storeDir . DIRECTORY_SEPARATOR . $Folder, 0755, true);
            endif;

            $this->setFolderDir(self::$storeDir . DIRECTORY_SEPARATOR . $Folder);
        }

    }

    //Verifica e monta o nome dos arquivos tratando a string!
    private function setFileName()
    {
        $this->Name = $this->Name . strrchr($this->File['name'], '.');
    }


    /**
     * Realiza o upload de imagens redimensionando a mesma!
     *
     */
    private function UploadImage()
    {
        switch ($this->File['type']):
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $this->Image = imagecreatefromjpeg($this->File['tmp_name']);
                break;
            case 'image/png':
            case 'image/x-png':
                $this->Image = imagecreatefrompng($this->File['tmp_name']);
                break;
        endswitch;

        if (!$this->Image):
            $this->Result = false;
            $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG!';
        else:
            $x = imagesx($this->Image);
            $y = imagesy($this->Image);
            $ImageX = ($this->Width < $x ? $this->Width : $x);
            $ImageH = ($this->Heigth < $y ? $this->Heigth : ($ImageX * $y) / $x);

            $NewImage = imagecreatetruecolor($ImageX, $ImageH);
            imagealphablending($NewImage, false);
            imagesavealpha($NewImage, true);
            imagecopyresampled($NewImage, $this->Image, 0, 0, 0, 0, $ImageX, $ImageH, $x, $y);

            switch ($this->File['type']):
                case 'image/jpg':
                case 'image/jpeg':
                case 'image/pjpeg':
                    imagejpeg($NewImage, $this->getFolderDir() . DIRECTORY_SEPARATOR . $this->Name);
                    break;
                case 'image/png':
                case 'image/x-png':
                    imagepng($NewImage, $this->getFolderDir() . DIRECTORY_SEPARATOR . $this->Name);
                    break;
            endswitch;

            if (!$NewImage):
                $this->Result = false;
                $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG!';
            else:
                $this->Result = $this->Name;
                $this->Error = null;
            endif;

            imagedestroy($this->Image);
            imagedestroy($NewImage);
        endif;
    }

    /**
     *
     */
    private function MoveFile()
    {

        try {
            move_uploaded_file($this->File['tmp_name'], $this->getFolderDir() . DIRECTORY_SEPARATOR . $this->Name);
            $this->Result = $this->Name;
            $this->Error = null;

        } catch (PDOException $e) {
            $this->Result = false;
            logger($e, gc_DANGER, MOVE_FILE_ERROR);
        }
    }

}