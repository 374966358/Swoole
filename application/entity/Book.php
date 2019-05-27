<?php

namespace entity;

use Anthony\MVC\Entity;

class Book extends Entity
{
    public function __construct(array $array)
    {
        parent::__construct($array);
    }

    /**
     * 对应数据库表名.
     */
    const MODLE_NAME = 'book';

    /**
     * 对应数据库的主键.
     */
    const PK_ID = 'id';

    /**
     * 设置要查询字段.
     */
    public $id;
    public $book;
}
