<?php

namespace Anthony\Coroutine;

class Context
{
    /**
     * @var \swoole_http_request
     */
    private static $request;

    /**
     * @var \swoole_http_response
     */
    private static $response;

    /**
     * @var array
     */
    private static $map = [];

    public function __construct(\swoole_http_request $request, \swoole_http_response $response)
    {
        self::$request = $request;
        self::$response = $response;
    }

    /**
     * @return \swoole_http_request
     */
    public function getRequest()
    {
        return self::$request;
    }

    /**
     * @return \swoole_http_response
     */
    public function getResponse()
    {
        return self::$response;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        self::$map[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed | null
     */
    public function get($key)
    {
    echo "你进入了key的世界：" . PHP_EOL;
    var_dump($key);
        if (isset(self::$map[$key])) {
            return self::$map[$key];
        }

        return null;
    }
}

