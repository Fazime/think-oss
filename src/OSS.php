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
    public function __construct( $bucket = '' )
    {
        
        $config = Config::get('oss');
        
        if( empty($config['endpoint']) || empty($config['accessKeyId']) || empty($config['accessKeySecret']) || empty($config['bucket']['default']) ) {
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
        return $this;
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
     * @param string $content  内存的内容
     * @param string $object   要保存的地址
     * @throws OssException;
     * @return array
     */
    public function put($content , $object)
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
     * @param string $local 本地文件地址
     * @param string $object object名称
     * @return array
     * @throws OssException
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
     * 读取文件到内存
     * @param $object
     * @param string $style 样式：image/resize,m_fixed,w_100,h_100/rotate,90| style/样式名
     * @param string $local 需要保存的本地路径
     * @param array $options
     * @return string
     */
    public function read( $object, $style = '', $local = '', $options = [])
    {
        $style && $options[self::OSS_PROCESS] = $style;
        $local && $options[self::OSS_FILE_DOWNLOAD] = $local;
        return $this->getObject($this->bucket, $object, $options);
    }
    
    /**
     * 复制
     * @param string $to_object
     * @param string $from_object
     * @param string $fromBucket
     * @return null
     * @throws OssException
     */
    public function copy($to_object, $from_object, $fromBucket = '')
    {
        try{
            $fromBucketPath = $fromBucket ? $this->getBucket($fromBucket) : $this->bucket;
            return $this->copyObject($fromBucketPath, $from_object, $this->bucket, $to_object);
        } catch(OssException $e) {
            throw new OssException($e->getMessage());
        }
    }
    
    /**
     * 判断是否存在对象
     * @param $object
     * @param $bucket
     * @return bool
     */
    public function has($object, $bucket = null)
    {
        return $this->doesObjectExist($bucket??$this->bucket, $object);
    }
    
    /**
     * 删除单个或多个对象
     * @param string|array $object
     */
    public function delete($object)
    {
        try {
            if (is_string($object)) {
                $this->deleteObject($this->bucket, $object);
            } elseif (is_array($object)) {
                $this->deleteObjects($this->bucket, $object);
            }
        } catch (\Exception $e) {
            throw new OssException($e->getMessage());
        }
    }
}