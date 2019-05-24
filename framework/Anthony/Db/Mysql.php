<?php

namespace Anthony\Db;

use Anthony\Core\Log;
use Swoole\Coroutine\Mysql as SwMySql;

class Mysql
{
    // 主数据库连接
    private static $master;
    // 从数据库连接
    private static $slave;
    // 配置文件
    private static $config;

    /**
     * @param $config
     * @return mixed
     * @throws \Exception
     * @desc 链接mysql
     */
    public function connect($config)
    {
        // 存储配置文件到全局变量中
        self::$config = $config;
        // 主数据库连接实例化Swoole中Mysql类
        $masterMysql = new SwMySql();
        // 主数据库连接
        $res = $masterMysql->connect(self::$config['master']);
        // 判断是否连接成功
        if (false === $res) {
            // 抛出异常
            throw new \Exception($masterRes->connect_error, $masterRes->connect_errno);
        } else {
            // 将数据库连接存储到全局变量中
            self::$master = $masterMysql;
        }

        if (isset(self::$config['slave']) && !empty(self::$confdig['slave'])) {
            // 从数据库连接实例化Swoole中Mysql类
            $slaveMysql = new Mysql();
            // 判断配置文件从配置库是一维还是多维数组
            if (count(self::$config['slave']) === count(self::$config['slave'], 1)) {
                // 从数据库连接
                $res = $slaveMysql->connect(self::$config['slave']);
                // 判断是否连接成功
                if (false === $res) {
                    // 抛出异常
                    throw new \Exception($slaveMysql->connect_error, $slaveMysql->connect_errno);
                } else {
                    // 将数据库连接存储到全局变量中
                    self::$slave[] = $slaveMysql;
                }
            } else {
                // 多维数组循环创建连接
                for ($i = 0; $i < count(self::$config['slave']); $i++) {
                    // 从数据库连接
                    $res = $slaveMysql->connect(self::$config['slave'][$i]);
                    // 判断是否连接成功
                    if (false === $res) {
                        // 抛出异常
                        throw new \Exception($slaveRes->connect_error, $slaveRes->connect_errno);
                    } else {
                        // 将数据库连接存储到全局变量中
                        self::$slave[] = $slaveMysql;
                    }
                }
            }
        }

        return $res;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @desc 利用__call实现操作MySQL并能做到断线重连等相关检测
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        // 判断使用主库还是从库
        $mysql = self::chooseDb($arguments[0]);
        // 定义全局DB
        $db = $mysql['db'];

        // 判断当前连接是否可用如果不可用执行重连
        if (!$db->connected) {
            // 获得重链后的数据
            $db = self::reconnect($mysql['type'], $mysql['index']);
            // 执行sql语句
            $result = $db->$name($arguments[0]);
        } else {
            // 执行sql语句
            $result = $db->$name($arguments[0]);
        }

        if (false === $result) {
            throw new \Exception($db->error, $db->errno);
        }

        // 返回数据
        return self::parseResult($result, $db);
    }

    /**
     * @param $sql
     * @desc 根据sql语句, 选择主还是从
     * @ 判断有select则选择从库, insert、update、delete使用主库
     * @return array
     */
    private static function chooseDb($sql)
    {
        // 截取sql语句前6位判断是否是select并且配置文件中有slave且不是没有空信息
        if (strtolower(substr($sql, 0, 6)) === 'select' && isset(self::$config['slave']) && !empty(self::$config['slave'])) {
            // 判断是否是一维数组还是多维数组
            if (count(self::$config['slave']) === count(self::$config['slave'], 1)) {
                // 如果是一维数组将索引定义为0
                $index = 0;
            } else {
                // 多维数组在配置文件中随机选择
                $index = array_rand(self::$slave);
            }

            // 返回从库数据链接信息
            return [
                // 用于区分主从类型
                'type' => 'slave',
                // 用于获取从数据链接索引
                'index' => $index,
                // 数据库链接信息
                'db' => self::$slave[$index],
            ];
        }

        // 返回主库数据链接信息
        return [
            // 用于区分主从类型
            'type' => 'master',
            // 类型定义
            'index' => 0,
            // 数据库链接信息
            'db' => self::$master,
        ];
    }

    /**
     * @param $type
     * @param $index
     * @return MySQL
     * @desc 单个数据库重连
     * @throws \Exception
     */
    private static function reconnect($type, $index)
    {
        // 判断是否是主数据库
        if ($type === 'master') {
            // 实例化swoole类中mysql
            $masterMysql = new SwMysql();
            // 根据配置文件连接mysql
            $res = $masterMysql->connect(self::$config['master']);
            // 判断连接成功OR失败
            if (false === $res) {
                // 抛出异常
                throw new \Excpetion($masterMysql->connect_error, $masterMysql->errno);
            } else {
                self::$master = $masterMysql;
            }

            return $masterMysql;
        }

        // 判断是否是从数据库
        if ($type === 'slave') {
            // 实例化swoole类中的mysql
            $slaveMysql = new SwMysql();
            // 根据配置文件连接mysql
            $res = $slaveMysql->connect(self::$config['slave'][$index]);
            // 判断连接成功OR失败
            if (false === $res) {
                // 抛出异常
                throw new \Excpetion($slaveMysql->connect_error, $slaveMysql->errno);
            } else {
                self::$slave[$index] = $slaveMysql;
            }

            return $slaveMysql;
        }
    }

    /**
     * @param $result
     * @param $db MySQL
     * @return array
     * @desc 格式化返回结果：查询：返回结果集, 插入：返回新增id, 更新与删除等操作：返回影响条数
     */
    private static function parseResult($result, $db)
    {
        // 判断等于true的只有insert、update、delete
        if (true === $result) {
            return [
                // 返回影响条数
                'affected_rows' => $db->affected_rows,
                // 返回添加id
                'insert_id' => $db->insert_id,
            ];
        }

        // 返回查询数据
        return $result;
    }
}

