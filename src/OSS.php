<?php

namespace fazi\oss;

use OSS\Core\OssException;
use OSS\OssClient;
use think\facade\Config;

class OSS extends OssClient
{
    public $bucket = '';
    public $ossClient;
    
    
    /**
     * OSS constructor.
     * @param string $bucket
     * @throws OssException
     */
    public function __construct($bucket = '')
    {
        
        $config = Config::get('oss');
        
        if (empty($config['endpoint']) || empty($config['accessKeyId']) || empty($config['accessKeySecret']) || empty($config['bucket']['default'])) {
            throw new OssException('请先设置文件中的endpoint、accessKeyId、accessKeySecret、bucket');
        }
        //默认BUCKET
        $this->bucket = $bucket && !empty($config['bucket'][$bucket]) ? $config['bucket'][$bucket] : $config['bucket']['default'];
        
        //创建实例
        parent::__construct($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint']);
        
    }
    
    /**
     * 切换bucket
     * @param string $bucket 配置里自定义的键值或原值
     */
    public function setBucket($bucket)
    {
        $this->bucket = $this->getBucket($bucket);
    }
    
    /**
     * 读取BUCKET：存在配置取配置值否则使用本值
     * @param string $bucket
     * @return string
     */
    public function getBucket($bucket)
    {
        $config = Config::get('oss.bucket');
        return $bucket && !empty($config[$bucket]) ? $config[$bucket] : $bucket;
    }
    
    /**
     * 上传内存中的内容
     * @param string $content 内存的内容
     * @param string $object  要保存的地址
     * @throws OssException;
     * @return array
     */
    public function put($content, $object)
    {
        try {
            return $this->putObject($this->bucket, $object, $content);
        } catch (OssException $e) {
            throw new OssException($e->getMessage());
        }
        
    }
    
    /**
     * 上传本地文件
     *
     * @param string $local  本地文件地址
     * @param string $object object名称
     * @throws OssException
     * @return array
     */
    public function upload($local, $object)
    {
        try {
            return $this->uploadFile($this->bucket, $object, $local);
        } catch (OssException $e) {
            throw new OssException($e->getMessage());
        }
    }
    
    
    /**
     * 读取文件到内存[存到本地]
     * @param string $object
     * @param string|null $save_file 路径
     * @return string
     */
    public function read($object, $save_file = null)
    {
        $options = $save_file ? [
            OssClient::OSS_FILE_DOWNLOAD => $save_file,
        ] : [];
        
        return $this->getObject($this->bucket, $object, $options);
    }
    
    /**
     * 复制
     * @param string $to_object
     * @param string $from_object
     * @param string $fromBucket
     * @throws OssException
     * @return null
     */
    public function copy($to_object, $from_object, $fromBucket = '')
    {
        try {
            $fromBucketPath = $fromBucket ? $this->getBucket($fromBucket) : $this->bucket;
            return $this->copyObject($fromBucketPath, $from_object, $this->bucket, $to_object);
        } catch (OssException $e) {
            throw new OssException($e->getMessage());
        }
    }
    
    /**
     * @param string $object      对象路径
     * @param null|string $bucket 临时指定BUCKET
     * @throws OssException
     * @return bool
     */
    public function has($object, $bucket = null)
    {
        try {
            return $this->doesObjectExist($bucket ?? $this->bucket, $object);
        } catch (OssException $e) {
            throw new OssException($e->getMessage());
        }
    }
    
    /**
     * 指定前缀（目录）搜索文件和目录
     * @param $path
     * @param null $bucket
     * @throws OssException
     * @return array[]
     */
    public function dir($path, $bucket = null)
    {
        //配置
        $options = [
            'delimiter' => '/',
            'prefix'    => $path,
            'max-keys'  => 1000,
            'marker'    => '/',
        ];
        
        try {
            $listObjectInfo = $this->listObjects($bucket ?? $this->bucket, $options);
            $dirInfo = $listObjectInfo->getPrefixList();
            $listInfo = $listObjectInfo->getObjectList();
            //格式
            $dir = $list = [];
            if (!empty($dirInfo)) {
                foreach ($dirInfo as $d) {
                    $dir[] = $d->getPrefix();
                }
            }
            if (!empty($listInfo)) {
                foreach ($listInfo as $l) {
                    $key = $l->getKey();
                    $key != $path && $list[] = $key;
                }
            }
            return [
                'dir'  => $dir,
                'list' => $list,
            ];
        } catch (OssException $e) {
            throw new OssException($e->getMessage());
        }
    }
}