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

    public function user()
    {
        return 456789123;
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
        $result = BookService::getInstance()->getBookById($this->request->get['id']);

        return json_encode($result);
    }

    public function insert()
    {
        $result = BookService::getInstance()->insert($this->request->get);

        return json_encode($result);
    }

    public function update()
    {
        $array = [
            'book' => $this->request->get['book']
        ];

        $result = BookService::getInstance()->updateById($array, $this->request->get['id']);

        return json_encode($result);
    }
}
