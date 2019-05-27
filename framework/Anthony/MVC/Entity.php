<?php

namespace Anthony\MVC;

class Entity
{
    /**
     * Entity constructor.
     *
     * @param array $array
     * @desc 把数组填充到Entity中
     */
    public function __construct(array $array)
    {
        echo 13 .PHP_EOL;
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
