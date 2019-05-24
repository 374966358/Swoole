<?php

namespace Anthony\Pool;

use Anthony\Db\Mysql as DB;

class Mysql
{
    // 用于存储当前对象
    private static $instance;
    // 用于存储process数据
    private static $pool;
    // 用于存储config配置文件
    private static $config;

    /**
     * @param null $config
     * @return Mysql
     * @desc 获取连接池实例
     * @throws \Exception
     */
    public static function getInstance($config = null)
    {
        // 判断是否已经实例化
        if (!self::$instance instanceof static) {
            // 判断数据库配置文件是否存在
            if (empty($config)) {
                throw new Exception('mysql config empty');
            }

            self::$instance = new static($config);
        }

        return self::$instance;
    }

    /**
     * @desc 防止克隆
     */
    public function __clone(){}

    /**
     * Mysql constructor.
     * @param $config
     * @throws \Exception
     * @desc 初始化，自动创建实例,需要放在workerstart中执行
     */
    public function __construct($config)
    {
        // 判断是否已经实例化好并存储在pool中
        if (empty(self::$pool)) {
            // 将配置文件赋予全局变量中
            self::$config = $config;
            // 声明管道长度
            self::$pool = new \chan(self::$config['pool_size']);
            // 循环并创建数据库连接
            for ($i = 0; $i < 1; $i++) {
                // 实例化数据库创建类
                $mysql = new DB();
                // 调用创建数据库
                $res = $mysql->connect(self::$config);
                // 判断是否创建数据库连接
                if (false === $res) {
                    throw new \Exception('failed to connect mysql error.');
                } else {
                    $this->put($mysql);
                }
            }
        }
    }

    /**
     * @param $mysql
     * @desc 放入一个mysql连接入池
     */
    public function put($mysql)
    {
        self::$pool->push($mysql);
    }

    /**
     * @return mixed
     * @desc 获取一个连接, 当超时返回异常
     * @throws \Exception
     */
    public function get()
    {
        $mysql = self::$pool->pop(self::$config['get_pool_timeout']);

        if (false === $mysql) {
            throw new \Exception('get mysql timeout, all mysql connection is null');
        }

        return $mysql;
    }

    /**
     * @return mixed
     * @desc 获取当时连接池可用对象
     */
    public function getLength()
    {
        return self::$pool->length();
    }
}

