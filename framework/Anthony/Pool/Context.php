<?php

namespace Anthony\Pool;

use Anthony\Coroutine\Coroutine;

/**
 * Class Context.
 *
 * @desc context pool, 请求之间隔离, 请求之内任何地方可以存取
 */
class Context
{
    /**
     * @var array context pool
     */
    private static $pool = [];

    /**
     * @return \Anthony\Coroutine\Context
     * @desc 可以任意协程获取到Context
     */
    public static function getContext()
    {
        $id = Coroutine::getPid();

        if (isset(self::$pool[$id])) {
            return self::$pool[$id];
        }

        return null;
    }

    /**
     * @desc 清除context
     */
    public static function clear()
    {
        $id = Coroutine::getPid();

        if (isset(self::$pool[$id])) {
            unset(self::$pool[$id]);
        }
    }

    /**
     * @param $context
     * @desc 设置content
     */
    public static function set($context)
    {
        $id = Coroutine::getPid();

        self::$pool[$id] = $context;
    }
}
