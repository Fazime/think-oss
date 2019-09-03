<?php


namespace fazi\oss;

use fazi\oss\command\InitCommand;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands(InitCommand::class);
    }
}
