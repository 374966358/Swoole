<?php

namespace Anthony\MVC;

use Anthony\Pool\Context;

class Controller
{
    protected $request;

    const _CONTROLLER_KEY_ = '__CTR__';
    const _METHOD_KEY_ = '__METHOD__';

    public function __construct()
    {
        $context = Context::getContext();
        $this->request = $context->getRequest();
    }
}
