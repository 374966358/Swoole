<?php

namespace Anthony\Core;

use Anthony\Anthony;
use Anthony\Helper\Dir;

class Config
{
    /**
     * @var 配置map
     */
    private static $configMap;

    /**
     * @desc 读取配置，默认是config/default.php
     */
    public static function load()
    {
        self::$configMap = require Anthony::$rootPath . DS . 'config' . DS . 'default.php';
    }

    public static function loadLazy()
    {
        $configDir = Dir::tree(Anthony::$rootPath . DS . 'config', "/.php$/");

        foreach ($configDir as $filed) {
            if (strstr($filed, 'default.php')) {
                continue;
            } else {
                self::$configMap[substr($filed, strripos($filed, DS) + 1, -4)] = include "{$filed}";
            }
        }
    }

    /**
     * @param $key
     * @desc 读取配置
     * @return string|null
     */
    public static function get($key)
    {
        if (isset(self::$configMap[$key])) {
            return self::$configMap[$key];
        }

        return null;
    }
}

