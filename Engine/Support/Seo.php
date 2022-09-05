<?php

namespace app\Machine\Engine\Support;

use app\Machine\Request;

class Seo
{

    private string $Title;
    private string $Content;
    private string $Link;
    private string $Image;

    /**
     * @param string $Title
     * @param string $Content
     * @param string $Link
     * @param string $Image
     */
    public function __construct()
    {
        $this->Title = $_ENV['APP_NAME'];
        $this->Content = '';
        $this->Link = $_ENV['APP_URL'];
        $this->Image = '';
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->Title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->Content;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->Link;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->Image;
    }


    /**
     * @param $seoTitle
     * @param $seoContent
     * @param $seoLink
     * @param null $seoImage
     */
    public function setMeta($seoTitle, $seoContent, $seoLink, $seoImage = ''): void
    {
        $this->Title = $seoTitle;
        $this->Content = $seoContent;
        $this->Link = $seoLink;
        $this->Image = $seoImage;
    }

}