<?php

return [
	//设置endpoint 默认公网，同区ECS可使用内网地址流量免费
    'endpoint'      => env('OSS_ENDPOINT'),
    //推荐使用子用户access key
    'accessKeyId'  => '',
    'accessKeySecret'  => '',
	//默认Bucket 实际业务可能用到多个BUCKET
    'bucket'  => '',

];
