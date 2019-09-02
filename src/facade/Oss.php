<?php

namespace fazi\oss\facade;

use think\Facade;

class OSS extends Facade
{
    protected static function getFacadeClass()
    {
        return 'fazi\oss\OSS';
    }
}
