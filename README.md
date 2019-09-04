# 阿里云OSS SDK for ThinkPHP6

## 概述

本包封装了Aliyun官方发布的SDK，使得能在ThinkPHP6里快速使用。

## 安装

- 运行 ***composer*** 命令:

        composer require fazi/think-oss

   或者在根目录的 `composer.json` 文件中添加：

        "require": {
            "fazi/think-oss": "^0.1"
        }
        
   然后运行命令 `composer install` 安装依赖。
   
-   依赖安装成功后，ThinkPHP6 根目录运行
        
        php think oss:init
        
    将在 ***.env*** 中添加 OSS的endpoint配置（在阿里云同区ESC环境可设成内网地址），并在 全局config 目录下生成 oss.php 配置文件。
   
 ##快速使用
 
 - 配置
 
    ***.env*** 里配置好阿里云endpoint
    
    ***/config/oss.php*** 阿里云子accessKeyIds相关配置
    
 - 目前未集成ThinkPHP6上传功能。所以需要在需要OSS上传的时候调用。
        
        $local = 'test/test.txt';
        $content = file_get_contents($local);
        $object = 'yun/test.txt';
        
        //实例并调用
        $oss = (new OSS());
        //从内存直接上传
        $oss->put($content, $object);
        
        //切换bucket
        $oss->setBucket('custom');#配置文件中自定义名
        //或者
        $oss->bucket = \think\facade\Config::get('oss.bucket.custom');
        
        //选取本地文件上传
        $oss->upload($local, $object);
        
 - 类继承了SDK的OssClient ，所以可以正常调用SDK的方法:
        
        $oss = (new OSS());
        $oss->putObject($bucket, $object, $content, $options);
                
 - 异常抛出实例为 OssException ，请在ThinkPHP6 ***app/ExceptionHandle.php*** 中做好异常接管，类似
 
        // OSS异常
        if ($e instanceof OssException) {
            return json($e->getError(), 501);
        }
        
 ##后言
 
官方SDK已经非常完善，本包没有缩减任何官方SDK的功能，只是封装并简化一些个人经常使用总结出来的方法。目前只推出 put 和 upload 方法。今后一定会丰富完善的。感谢大家的支持。
