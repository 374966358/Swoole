<?php

namespace controller;

use service\Book as BookService;

class Index
{
    public function index()
    {
        try {
            return 'i am family by route!';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function tong()
    {
        return 'tong hahahaha';
    }

    public function list()
    {
        echo 1 .PHP_EOL;
        $result = BookService::getInstance()->getBookList();
        echo 700 .PHP_EOL;

        return json_encode($result);
    }
}
