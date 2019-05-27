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
        $result = BookService::getInstance()->getBookList();

        return json_encode($result);
    }
}
