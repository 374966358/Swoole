<?php

use Anthony\Anthony;

return [
    // 模板页面的存放目录
    'path' => Anthony::$applicationPath . DS . 'template' . DS . 'default', // 模版目录, 空则默认 template/default
    // 模板配置文件
    'config' => [
        // 配置缓存目录
        'cache' => Anthony::$applicationPath . DS . 'template' . DS . 'default_cache',
        // 配置是否自动更新缓存文件
        'auto_reload' => true,
        // 配置是否开启debug
        'debug' => true,
    ],
];

