<?php


namespace Fazi\oss;

use Fazi\oss\command\InitCommand;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands(InitCommand::class);
    }
}
