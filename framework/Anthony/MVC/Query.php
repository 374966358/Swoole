<?php

namespace Anthony\MVC;

use Anthony\Coroutine\Coroutine;
use Anthony\Pool\Mysql as PoolMysql;

class Query
{
    /**
     * @desc entity名称
     */
    private $entity;

    /**
     * @var mysql连接数组
     * @desc 不同协程不能复用mysql连接，所以通过协程id进行资源隔离
     */
    private $connections;

    /**
     * @var mysql连接模型对象
     */
    private $mysql;

    /**
     * @desc 对应数据库表名
     */
    private $name;

    /**
     * @desc 对应数据库主键
     */
    private $pk;

    public function __construct($entity)
    {
        self::$entity = $entity;
        // 获取协程ID
        $coId = Coroutine::getId();
        // 判断当前协程中是否存在连接如果不存在执行链接操作
        if (!isset($this->connections[$coId]) && empty($this->connections[$coId])) {
            // 获取数据连接并存储在协程ID中共用
            $this->connections[$coId] = PoolMysql->getInstance()->get();
            // 使用反射类获取查询表的配置信息
            $entityRef = new \ReflectionClass(self::$entity);
            // 获取数据表名称
            $this->name = $entityRef->getConstant('MODLE_NAME');
            // 获取数据表中主键
            $this->pk = $entityRef->getConstant('PK_ID');
            // 在协程结束时调用
            defer(function () {
                $this->recycle();
            });
        }
        $this->mysql = $this->connections[$coId];
    }

    /**
     * @desc Mysql资源池回收
     */
    public function recycle()
    {
        $coId = Coroutine::getId();

        if (isset($this->connections[$coId]) && !empty($this->connections[$coId])) {
            PoolMysql->getInstance()->put($this->connections[$coId]);
            unset($this->connections[$coId]);
        }
    }

    /**
     * @return mixed
     * @desc 获取表名
     */
    public function getLibName()
    {
        return self::$name;
    }

    /**
     * @param $id
     * @param string $field
     * @return mixed
     * @desc 通过主键查询记录
     */
    public function fetchById($id, $field)
    {
        return $this->fetchEntity("{$this->pk} = {$id}", $field);
    }

    /**
     * @param string $where
     * @param string $field
     * @param null $orderBy
     * @return mixed
     * @desc 通过条件查询一条记录, 并返回一个entity
     */
    public function fetchEntity($where = '', $fields = '*', $orderBy = null)
    {
        $result = $this->fetchArray($where, $fields, $orderBy);

        var_dump("MVC\Query\\fetchEntity查询出返回数据：" . $result);

        if (!empty($result[0])) {
            return new $this->entity($result[0]);
        }

        return null;
    }

    /**
     * @param string $where
     * @param string $field
     * @param null $orderBy
     * @param int $limit
     * @return mixed
     * @desc 通过条件查询
     */
    public function fetchArray($where = '', $fields = '*', $orderBy = null, $limit = 0)
    {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $query .= " LIMIT {$limit}";
        }

        return $this->mysql->query($query);
    }

    /**
     * @param string $where
     * @param string $fields
     * @param null $orderBy
     * @param int limit
     * @return mixed
     * @desc 通过条件查询记录列表, 并返回entity列表
     */
    public function fetchAll($where = '', $fields = '*', $orderBy = null, $limit = 0)
    {
        $result = $this->fetchArray($where, $fields, $orderBy, $limit);

        if (empty($result)) {
            return $result;
        }

        foreach ($result as $index => $value) {
            $result[$index] = new $this->entity($value);
        }

        return $result;
    }

    /**
     * @param array $array
     * @return bool
     * @desc 插入一条记录
     */
    public function insert(array $array)
    {
        foreach ($array as $name => $value) {
            $strFields .= "`{$this->mysql->escape($name)}`,";
            $strValues .= "'{$this->mysql->escape($value)}',";
        }

        $strFields = rtrim($strFields, ',');
        $strValues = rtrim($strValues, ',');

        $query = "INSERT INTO {$this->name} ({$strFields}) VALUES ({$strValues})";
        $result = $this->mysql->query($query);

        if (!empty($result['insert_id'])) {
            return $result['insert_id'];
        }

        return false;
    }

    /**
     * @param array $array
     * @param $where
     * @return bool
     * @throws \Exception
     * @desc 按条件更新记录
     */
    public function update(array $array, $where)
    {
        if (empty($where)) {
            throw new \Exception('update must have a where condition');
        }

        $strUpdateFields = '';

        foreach ($array as $key => $value) {
            $strUpdateFields .= "`{$this->mysql->escape($key)}` = '{$this->mysql->escape($value)}',";
        }

        $query = "UPDATE {$this->name} SET {$strUpdateFields} WHERE {$where}";

        $result = $this->mysql->query($query);

        return $result['affected_rows'];
    }

    /**
     * @param $where
     * @return mixed
     * @throws \Exception
     * @desc 按条件删除记录
     */
    public function delete($where)
    {
        if (empty($where)) {
            throw new \Exception('delete must have a where condition');
        }

        $query = "DELETE FROM {$this->name} WHERE {$where}";

        $result = $this->mysql->query($query);

        return $result['affected_rows'];
    }
}

