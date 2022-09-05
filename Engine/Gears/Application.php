<?php
//
//namespace app\Machine\Engine\Gears;
//
//class Application
//{
//
//    /**
//     * The gcoffee framework version.
//     *
//     * @var string
//     */
//    const VERSION = '1.0.1';
//
//    /**
//     * The base path for the gcoffee installation.
//     *
//     * @var string
//     */
//    protected string $basePath;
//
//    /**
//     * The custom database path defined by the developer.
//     *
//     * @var string
//     */
//    protected $databasePath;
//
//    /**
//     * The custom storage path defined by the developer.
//     *
//     * @var string
//     */
//    protected $storagePath;
//
//    /**
//     * The custom application path defined by the developer.
//     *
//     * @var string
//     */
//    protected $appPath;
//
//    /**
//     * Create a new gcoffee application instance.
//     *
//     * @param string|null $basePath
//     * @return void
//     */
//    public function __construct(string $basePath = null)
//    {
//        if ($basePath) {
//            $this->setBasePath($basePath);
//        }
//    }
//
//    public function rootPath()
//    {
//        return str_replace('\app\Machine\Engine', '', dirname(__DIR__));
//    }
//
//    /**
//     * Get the version number of the application.
//     *
//     * @return string
//     */
//    public function version()
//    {
//        return static::VERSION;
//    }
//
//    /**
//     * Get the path to the application configuration files.
//     *
//     * @param  string  $path Optionally, a path to append to the config path
//     * @return string
//     */
//    public function configPath($path = '')
//    {
//        return $this->rootPath().DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
//    }
//
//
//
//
//    /**
//     * Set the base path for the application.
//     *
//     * @param string $basePath
//     * @return $this
//     */
//    public function setBasePath(string $basePath): Application
//    {
//        $this->basePath = rtrim($basePath, '\/');
//
//        //$this->bindPathsInContainer();
//
//        return $this;
//    }
//
//    /**
//     * Get the path to the application "app" directory.
//     *
//     * @param  string  $path
//     * @return string
//     */
//    public function path($path = '')
//    {
//        $appPath = $this->appPath ?: $this->basePath.DIRECTORY_SEPARATOR.'app';
//
//        return $appPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
//    }
//
//    /**
//     * Get the base path of the Laravel installation.
//     *
//     * @param string $path Optionally, a path to append to the base path
//     * @return string
//     */
//    public function basePath(string $path = ''): string
//    {
//        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
//    }
//
//    /**
//     * Get the path to the public / web directory.
//     *
//     * @return string
//     */
//    public function publicPath()
//    {
//        return $this->rootPath().DIRECTORY_SEPARATOR.'public';
//    }
//
//    /**
//     * Get the path to the database directory.
//     *
//     * @param  string  $path Optionally, a path to append to the database path
//     * @return string
//     */
//    public function databasePath($path = '')
//    {
//        return ($this->databasePath ?: $this->rootPath().DIRECTORY_SEPARATOR.'database').($path ? DIRECTORY_SEPARATOR.$path : $path);
//    }
//
//
//    /**
//     * Get the path to the storage directory.
//     *
//     * @return string
//     */
//    public function storagePath()
//    {
//        return $this->storagePath ?: $this->rootPath().DIRECTORY_SEPARATOR.'storage';
//    }
//
//    /**
//     * Get the path to the resources directory.
//     *
//     * @param  string  $path
//     * @return string
//     */
//    public function resourcesPath($path = '')
//    {
//        return $this->rootPath().DIRECTORY_SEPARATOR.'resources'.($path ? DIRECTORY_SEPARATOR.$path : $path);
//    }
//
//    public function configuration()
//    {
//        return new LoadConfiguration();
//    }
//}