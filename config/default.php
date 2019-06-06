<?php

return [
    'host' => '0.0.0.0',
    'port' => '9504',
    'httpServerSet' => [
        'worker_num' => 1,
    ],
    'mysql' => [
        'pool_size' => 10,
        'get_pool_timeout' => 0.5,
        'master' => [
            'host' => '127.0.0.1',   //数据库ip
            'port' => 3306,          //数据库端口
            'user' => 'root',        //数据库用户名
            'password' => 'root', //数据库密码
            'database' => 'xingchen',   //默认数据库名
            'timeout' => 0.5,       //数据库连接超时时间
            'charset' => 'utf8mb4', //默认字符集
            'strict_type' => true,  //ture，会自动表数字转为int类型
        ],
    ],
    'router' => function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/users', ['controller\Index', 'list']);
        $r->addRoute('GET', '/user/{bid:\d+}', 'controller\Index@getOne');
        $r->get('/add', ['controller\Index', 'insert']);
        $r->get('/test', function () {
            return 'i am test';
        });
        $r->post('/post', function () {
            return 'must post method';
        });
    },
];
