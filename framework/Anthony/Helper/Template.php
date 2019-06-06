<?php

namespace Anthony\Helper;

use Anthony\Core\Singleton;
use Twig;
use Anthony\Core\Config;

class Template
{
    use Singleton;

    public $template;

    public function __construct()
    {
        $templateConfig = Config::get('template');
        $loader = new \Twig_Loader_Filesystem($templateConfig['path']);
        $this->template = new \Twig_Environment($loader,array(
            'cache' => $templateConfig['cache'],
        ));
    }
}

