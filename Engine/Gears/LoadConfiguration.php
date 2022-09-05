<?php

namespace app\Machine\Engine\Gears;

use Exception;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class LoadConfiguration
{

    public function bootstrap(Application $app)
    {
        $items = [];

        $config = new Repositories($items);
        $this->loadConfigurationFiles($app, $config);
    }

    /**
     * @throws Exception
     */
    protected function loadConfigurationFiles(Application $app, Repositories $repository)
    {
        $files = $this->getConfigurationFiles($app);var_dump($files);

        if (! isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        foreach ($files as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        $configPath = realpath($app->configPath());

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    protected function getNestedDirectory(SplFileInfo $file, $configPath)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested).'.';
        }

        return $nested;
    }


}