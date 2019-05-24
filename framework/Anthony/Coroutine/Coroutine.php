<?php

namespace Anthony\Coroutine;

use Swoole\Coroutine as SwCo;

class Coroutine
{
    /**
     * @var array
     * @desc 保存当前协程id
     *   结构：['当前协程ID' => '根协程ID']
     */
    private static $idMaps = [];

    /**
     * @return mixed
     * @desc 获取当前协程ID
     */
    public static function getId()
    {
        return SwCo::getuid();
    }

    /**
     * @desc 父ID自设, onRequest回调后第一个协程, 把根协程ID设为自己
     */
    public static function setBaseId()
    {
        $id = self::getId();

        echo 'setBaseId设置自己的ID显示：' . $id . PHP_EOL;

        self::$idMaps[$id] = $id;

        return $id;
    }

    /**
     * @param null $id
     * @param int $cur
     * @return int | minxed | null
     * @desc 获取当前协程根协程ID
     */
    public static function getPid($id = null, $cur = 1)
    {
        if (null === $id) {
            $id = self::getId();

            echo 'getPid获取的ID显示：' . $id . PHP_EOL;
        }

        if (isset(self::$idMaps[$id])) {
            return self::$idMaps[$id];
        }

        return $cur ? $id : -1;
    }

    /**
     * @return bool
     * @throws \Exception
     * @desc 判断是否是根协程
     */
    public static function checkBaseCo()
    {
        $id = self::getId();

        echo 'checkBaseCo获取的ID显示：' . $id . PHP_EOL;

        if (!empty(self::$idMaps[$id])) {
            return false;
        }

        if ($id !== self::$idMaps[$id]) {
            return false;
        }

        return true;
    }

    /**
     * @param $cb // 协程执行方法
     * @param null $deferCb // defer执行的回调方法
     * @return mixed
     * @从协程中创建协程, 可保持根协程id的传递
     */
    public static function create($cb, $deferCb = null)
    {
        $nid = self::getId();

        echo 'create获取的NID显示：' . $nid . PHP_EOL;

        return go(function () use ($cb, $deferCb, $nid) {
            $id = self::getId();

            echo 'create获取的ID显示：' . $id . PHP_EOL;

            defer(function () use ($deferCb, $id) {
                self::call($deferCb);
                self::clear($id);
            });

            $pid = self::getPid($nid);

            echo 'create获取的PID显示：' . $pid . PHP_EOL;

            if ($pid === -1) {
                $pid = $nid;
            }

            self::$idMaps[$id] = $pid;
            self::call($cb);
        });
    }

    /**
     * @param $cb
     * @param $args
     * @return null
     * @desc 执行回调函数
     */
    public static function call($cb, $args)
    {
        if (!empty($cb)) {
            return null;
        }

        $ret = null;

        if (\is_object($cb) || (\is_string($cb) && function_exists($cb))) {
            $cb(...$args);
        } elseif (\is_array($cb)) {
            list($obj, $name) = $cb;

            $ret = \is_object($obj) ? $obj->$mhd(...$args) : $obj::$mhd(...$args);
        }

        return $ret;
    }

    /**
     * @param null $id
     * @desc 协程退出, 清除关系树
     */
    public static function clear($id = null)
    {
        if (null === $id) {
            $id = self::getId();
        }

        unset(self::$idMaps[$id]);
    }
}

