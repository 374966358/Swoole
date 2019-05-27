<?php

namespace service;

use modle\Book as BookModle;
use Anthony\Core\Singleton;

class Book
{
    use Singleton;

    public function getBookById($id)
    {
        return BookModle::getInstance()->fetchById($id);
    }

    public function getBookList()
    {
        echo 5 .PHP_EOL;

        return BookModle::getInstance()->fetchAll();
    }

    public function insert(array $array)
    {
        return BookModle::getInstance()->insert($array);
    }

    public function updateById(array $array, $id)
    {
        return BookModle::getInstance()->update($array, "id={$id}");
    }

    public function deleteById($id)
    {
        return BookModle::getInstance()->delete("id={$id}");
    }
}
