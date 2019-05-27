<?php

namespace Anthony\Core;

use SeasLog;
use Anthony\Anthony;

class Log
{
    public static function init()
    {
        SeasLog::setBasePath(Anthony::$applicationPath . DS . 'log');
    }

    public static function __callStatic($name, $arguments)
    {
        var_dump($arguments);
        forward_static_call_array(['SeasLog', $name], $arguments);
    }
}

