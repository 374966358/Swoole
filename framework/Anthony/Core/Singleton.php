<?php

namespace Anthony\Core;

trait Singleton
{
    private static $instance;

    public static function getInstance(...$args)
    {
        var_dump("Anthony\Core\Singleton参数：") . var_dump($args) . PHP_EOL;

        if (!self::$instance instanceof static) {
            self::$instance = new static(...$args);
        }

        return self::$instance;
    }
}

