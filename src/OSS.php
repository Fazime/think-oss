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
	 * @param string $bucket 配置里自定义的键值
	 */
	public function setBucket($bucket)
	{
		$config = Config::get('oss.bucket');
		$this->bucket = $bucket && !empty($config[$bucket]) ? $config[$bucket] : $config['default'];
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
	 * 下载OBJECT到内存
	 * @param $object
	 * @return string
	 * @throws OssException
	 */
	public function read( $object )
	{
		try{
			return $this->getObject($this->bucket, $object);
		} catch(OssException $e) {
			throw new OssException($e->getMessage());
		}
	}
}