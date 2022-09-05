<?php

namespace app\Machine\Engine\Gears;

use Illuminate\Support\Arr;

class Repositories
{

    /**
     * All the configuration items.
     *
     * @var array
     */
    protected $items = [];


    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param $key
     * @param $default
     * @return void|null
     */
    public function get($key, $default)
    {
        $key = explode('.', $key);

        $configFile = rootDir() . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $key[0].'.php';

        if (file_exists($configFile)){
            $required = require $configFile;
            return $required[
                $key[1]
            ];
        }else{
            return null;
        }

    }

    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];
        foreach ($keys as $key => $value) {
            Arr::set($this->items, $key, $value);
        }
    }


}