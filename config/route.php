<?php

return [
    function (FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/users', ['controller\Index', 'list']);
        $r->addRoute('GET', '/user/{bid:\d+}', 'controller\Index@getOne');
        $r->get('/add', ['controller\Index', 'insert']);
        $r->get('/test', function () {
            return 'i am test';
        });
        $r->post('/post', function () {
            return 'must post method';
        });
    }
];

