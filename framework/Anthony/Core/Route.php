<?php

namespace Anthony\Core;

class Route
{
    public static function dispatch($path)
    {
        if ($path === '/favicon.ico') {
            return;
        }

        if ($path === '/') {
            $controller = 'Index';
            $method = 'Index';
        } else {
            $maps = explode('/', $path);
            $controller = $maps[1];
            $method = $maps[2];
        }

        $controllerClass = "controller\\{$controller}";

        $class = new $controllerClass;

        return $class->$method();
    }
}

