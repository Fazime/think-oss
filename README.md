# 阿里云OSS SDK for ThinkPHP6

## 概述

本包封装了Aliyun官方发布的SDK，使得能在ThinkPHP6里快速使用。适用于单个或多个BUCKET间操作。

## 安装

- 运行 ***composer*** 命令:

        composer require fazi/think-oss

   或者在根目录的 `composer.json` 文件中添加：

        "require": {
            "fazi/think-oss": "^1.0"
        }
        
   然后运行命令 `composer install` 安装依赖。
   
-   依赖安装成功后，ThinkPHP6 根目录运行
        
        php think oss:init
        
    将在 ***.env*** 中添加 OSS的endpoint配置，并在 全局config 目录下生成 oss.php 配置文件。
   
 ## 快速使用
 
 - 配置
 
    ***.env*** 里配置好阿里云endpoint （在阿里云同区ECS环境可设成内网地址）
    
    ***/config/oss.php*** 阿里云子accessKeyIds相关配置
    
    配置文件说明
    
        return [
            //设置endpoint 默认公网，同区ECS可使用内网地址流量免费
             'endpoint'      => Env::get('OSS_ENDPOINT'),
             //推荐使用子用户access key
             'accessKeyId'  => '',
             'accessKeySecret'  => '',
            //默认Bucket 实际业务可能用到多个BUCKET
             'bucket'  => [
                'default' => '',#默认
                'custom' => '',#自定义
             ],
         ];
             
 - 目前未集成ThinkPHP6上传功能。所以需要在需要OSS上传的时候调用。
 
        use fazi\oss\OSS; 
        
        $local = 'test/test.txt';
        $content = file_get_contents($local);
        $object = 'yun/test.txt';
        
        //实例并调用
        $oss = (new OSS());
        //常用方法1：从内存直接上传
        $oss->put($content, $object);
        
        //切换bucket
        $oss->setBucket('custom');#配置文件中自定义名，不存在则取该值
        //或者直接赋值
        $oss->bucket = \think\facade\Config::get('oss.bucket.custom');
        
        //常用方法2：选择本地文件上传
        $oss->upload($local, $object);
        
        //常用方法3.1：读取云文件到内存
        $data = $oss->read($object);


        //常用方法3.2：直接保存云文件到本地（可选样式）
        //样式：image/resize,m_fixed,w_100,h_100/rotate,90| style/样式名
        $data = $oss->read($object, $style, $local);
        
        //常用方法4：复制云到云（支持不同Bucket间操作）
        $oss->copy($to_object, $from_object, $form_bucket);#$form_bucket为配置文件中自定义的键值，不存在则取该值。为空则为当前BUCKET。
        
        
 - 支持facade调用(使用默认‘default’ bucket):
 
        use fazi\oss\facade\OSS;
        OSS::upload($local,$object);
 
 - 类继承了SDK的OssClient ，所以可以正常调用SDK的方法:
        
        $oss = (new OSS());
        $oss->putObject($bucket, $object, $content, $options);
                
 - 异常抛出实例为 OssException ，请在ThinkPHP6 ***app/ExceptionHandle.php*** 中做好异常接管，类似
 
        // OSS异常
        if ($e instanceof OssException) {
            return json($e->getError(), 501);
        }
        
## 更新
2021-06-07 read方法增加参数（样式、本地路径、自定义参数）  
2021-01-29 增加方法delete删除对象（支持单个或多个对象）  
2020-11-19 增加方法dir遍历目录（目录或文件列表）  
2020-11-12 增加方法has判断对象是否存在  
2020-07-22 增加方法read和copy。因为已经开始用TP6重构之前的项目。故会继续更新。分享总结我使用OSS的经验  

## 后言
   
官方SDK已经非常完善，本包没有缩减任何官方SDK的功能，只是封装并简化一些个人经常使用总结出来的方法。目前只推出 put 和 upload 方法。
今后一定会丰富完善的。感谢大家的支持。欢迎访问我的个人主页 https://www.fazi.me/
