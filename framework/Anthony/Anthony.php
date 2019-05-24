<?php

namespace Anthony;

use Swoole;
use Anthony\Core\Config;
use Anthony\Core\Route;
use Anthony\Core\Log;
use Anthony\Coroutine\Coroutine;
use Anthony\Coroutine\Context;

class Anthony
{
    /**
     * @var 根目录
     */
    public static $rootPath;

    /**
     * @var 框架目录
     */
    public static $frameworkPath;

    /**
     * @var 程序目录
     */
    public static $applicationPath;

    final public static function run()
    {
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        self::$rootPath = dirname(dirname(__DIR__));
        self::$frameworkPath = self::$rootPath . DS . 'framework';
        self::$applicationPath = self::$rootPath . DS . 'application';

        // 注册自动加载类
        \spl_autoload_register(__CLASS__ . '::autoLoader');

        // 引入配置文件
        Config::load();

        // 日志初始化
        Log::init();

        $http = new Swoole\Http\Server(Config::get('host'), Config::get('port'));

        $http->set(Config::get('httpServerSet'));

        $http->on('WorkerStart', function (\swoole_http_server $serv, $worker_id) {
            if (function_exists('opcache_reset')) {
                \opcache_reset();
            }

            try {
                $mysqlConfig = Config::get('mysql');
                if (!empty($mysqlConfig)) {
                    Pool\Mysql::getInstance($mysqlConfig);
                }
            } catch (\Exception $e) {
                print($e);
                $serv->shutdown();
            } catch (\Throwable $throwable) {
                print($throwable);
                $serv->shutdown();
            }
        });

        $http->on('request', function ($request, $response) {
            try {
                // 初始化根协程ID
                Coroutine::setBaseId();

                // 初始化上下文
                $context = new Context($request, $response);

                // 存放容器pool
                Pool\Context::set($context);

                // 协程退出, 自动清空
                defer(function () {
                    Pool\Context::clear();
                });

                $result = Route::dispatch($request->server['path_info']);
                $response->end($result);
            } catch (\Exception $e) {
                Log::alert($e->getMessage(), $e->getTrace());
                $response->status(500);
            } catch (\Error $e) {
                Log::emergency($e->getMessage(), $e->getTrace());
                $response->status(500);
            } catch (\throwable $e) {
                Log::emergency($e->getMessage(), $e->getTrace());
                $response->status(500);
            }
        });

        $http->start();
    }

    /**
     * @param $class
     * @param 自动加载类
     */
    final public static function autoLoader($class)
    {
        // 定义rootPath
        $rootPath = dirname(dirname(__DIR__));

        // 把类转为目录，eg \a\b\c => /a/b/c.php
        $classPath = str_replace('\\', DS, $class) . '.php';

        // 约定框架类都放在framework目录下，业务类都在application下
        $findPath = [
            $rootPath . DS . 'framework' . DS,
            $rootPath . DS . 'application' . DS,
        ];

        // 遍历目录，查找文件
        foreach ($findPath as $path) {
            // 如果找到文件，则require进来
            $realPath = $path . $classPath;

            if (is_file($realPath)) {
                require "{$realPath}";
                return;
            }
        }
    }
}

