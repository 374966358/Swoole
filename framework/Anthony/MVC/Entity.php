<?php

namespace Anthony\MVC;

class Entity
{
    /**
     * Entity constructor
     * @param array $array
     * @desc 把数组填充到Entity中
     */
    public function __construct(array $array)
    {
        var_dump("我是Anthony\Entity中this信息为：" . $this);
        if (empty($array)) {
            return $this;
        }

        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}

