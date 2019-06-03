<?php

namespace Anthony\Core;

use Anthony\Pool\Context;
use Anthony\Core\Config;
use function FastRoute\simpleDispatcher;
use FastRoute\Dispatcher;

class Route
{
    public static function dispatch()
    {
        $context = Context::getContext();

        $request = $context->getRequest();

        $path = $request->getUri()->getPath();

        if ($path === '/favicon.ico') {
            return;
        }

        $route = Config::get('route');

        $dispatcher = simpleDispatcher($route);

        $routeInfo = $dispatcher->dispatch($request->getMethod(), $path);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // 并未匹配到路由
                echo 456;
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                // 匹配到路由但是请求类型错误
                throw new Exception('Method Error');
                break;
            case Dispatcher::FOUND:
                // 正确匹配到路由
                var_dump($routeInfo[1]);
                if (is_array($routeInfo[1])) {
                    var_dump($routeInfo[2]);
                    var_dump(is_array($routeInfo[2]));
                } elseif (is_callable($routeInfo[1])) {
                    // 如果只是回调函数返回回调函数中信息
                    return $routeInfo[1](...$routeInfo[2]);
                }
                break;
        }

        /*if ($path === '/favicon.ico') {
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
        var_dump($class->$method());

        return $class->$method();*/
    }
}

