<?php

namespace modle;

use Anthony\MVC\Query;
use Anthony\Core\Singleton;

class Book extends Query
{
    use Singleton;

    public function __construct()
    {
        parent::__construct('\entity\Book');
    }

    public static function asdf()
    {
        echo 123;die;
    }
}

