<?php

namespace modle;

use Anthony\MVC\Query;
use Anthony\Core\Singleton;

class Book extends Query
{
    use Singleton;

    public function __construct()
    {
        echo 3 .PHP_EOL;
        parent::__construct('entity\Book');
    }
}
