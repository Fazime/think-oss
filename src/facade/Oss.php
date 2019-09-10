<?php

namespace Fazi\oss\facade;

use think\Facade;

class OSS extends Facade
{
    protected static function getFacadeClass()
    {
        return 'Fazi\oss\OSS';
    }
}
