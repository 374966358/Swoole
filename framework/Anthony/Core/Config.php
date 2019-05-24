<?php

namespace Anthony\Core;

use Anthony\Anthony;

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

