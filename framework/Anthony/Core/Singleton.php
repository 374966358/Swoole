<?php

namespace Anthony\Core;

trait Singleton
{
    private static $instance;

    public static function getInstance(...$args)
    {
        if (!self::$instance instanceof static) {
            self::$instance = new static(...$args);
        }

        return self::$instance;
    }
}
