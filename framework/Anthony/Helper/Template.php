<?php

namespace Anthony\Helper;

use Anthony\Core\Singleton;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Anthony\Core\Config;

class Template
{
    use Singleton;

    public $template;

    public function __construct()
    {
        $templateConfig = Config::get('template');
        $loader = new FilesystemLoader($templateConfig['path']);
        $this->template = new Environment($loader, $templateConfig['config']);
    }
}

