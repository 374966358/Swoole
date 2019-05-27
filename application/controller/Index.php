<?php

namespace controller;

use service\Book as BookService;
use Anthony\MVC\Controller;

class Index extends Controller
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

    public function getOne()
    {
        var_dump($this->request->get);

        $result = BookService::getInstance()->fetchById($id);

        return json_encode($result);
    }
}
