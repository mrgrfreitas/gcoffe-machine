<?php


namespace app\Machine\Engine\Helpers;


/**
 * Class Checker
 * @package app\Machine\Engine\Helpers
 */
trait Checker
{

    public static function mimeTypeForImage($key)
    {
        $mimeType = [
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml'
        ];
        return array_search($key, $mimeType);
    }

    public static function mimeTypeForMedia($key)
    {
        $mimeType = [
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'flv'   => 'video/x-flv'
        ];
        return array_search($key, $mimeType);
    }

    public static function mimeTypeForArchives($key)
    {
        $mimeType = [
            'txt'   => 'text/plain',
            'htm'   => 'text/html',
            'html'  => 'text/html',
            'php'   => 'text/html',
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            'json'  => 'application/json',
            'xml'   => 'application/xml',

            // archives
            'swf'   => 'application/x-shockwave-flash',
            'zip'   => 'application/zip',
            'rar'   => 'application/x-rar-compressed',
            'exe'   => 'application/x-msdownload',
            'msi'   => 'application/x-msdownload',
            'cab'   => 'application/vnd.ms-cab-compressed',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
        ];
        return array_search($key, $mimeType);
    }

}