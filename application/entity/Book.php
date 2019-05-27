<?php

namespace entity;

use Anthony\MVC\Entity;

class book extends Entity
{
    /**
     * 对应数据库表名
     */
    CONST $MODLE_NAME = 'book';

    /**
     * 对应数据库的主键 
     */
    CONST $PK_ID = 'id';

    /**
     * 设置要查询字段
     */
    public $id;
    public $book;
}

