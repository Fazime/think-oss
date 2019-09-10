<?php


namespace Fazi\oss\command;

use think\console\Input;
use think\console\Output;
use think\facade\App;

class InitCommand extends \think\console\Command
{
    public function configure()
    {
        $this->setName('oss:init')
            ->setDescription('create oss config file');
    }

    public function execute(Input $input, Output $output)
    {
        $path = app()->getAppPath().'..'.DIRECTORY_SEPARATOR.'.env';
	    if (file_exists($path)
		    && strpos(file_get_contents($path), '[OSS]')
	    ) {
		    $output->writeln('OSS ENV is exists');
	    } else {
		    file_put_contents($path,
			    PHP_EOL . "[OSS]" . PHP_EOL . "ENDPOINT=oss-cn-shenzhen.aliyuncs.com" . PHP_EOL,
			    FILE_APPEND);
		    $output->writeln('OSS ENV has set');
	    }
        $this->createConfig($output);
    }

    public function createConfig(Output $output)
    {
        $configFilePath = app()->getAppPath().'..'.DIRECTORY_SEPARATOR.'config'
            .DIRECTORY_SEPARATOR.'oss.php';

        if (is_file($configFilePath)) {
            $output->writeln('Config file is exist');

            return;
        }
        $res = copy(__DIR__.DIRECTORY_SEPARATOR.'..'
            .DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR
            .'config.php', $configFilePath);
        if ($res) {
            $output->writeln('Create config file success:'.$configFilePath);
        } else {
            $output->writeln('Create config file error');
        }
    }
}
