<?php

namespace controller;

use Anthony\Pool\Context;
use Anthony\Pool\Mysql as PoMysql;

class Index
{
    public function index()
    {
        try {
            // 通过context拿到$request, 再也不用担心数据错乱了
            //$context = Context::getContext();
            //$request = $context->getRequest();
            $a = PoMysql::get();
            //$b = $a->query('SELECT * from book');
            $b = $a->query('UPDATE book set book = "哈哈" where id = 11');
            //$b = $a->query('INSERT book(`book`) values("你猜呢")');
            PoMysql::put($a);
            return 'i am family by route!' . json_encode($b);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function tong()
    {
        return 'tong hahahaha';
    }
}

