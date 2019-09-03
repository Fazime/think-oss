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
		
		if( !empty($config['endpoint']) || !empty($config['accessKeyId']) || !empty($config['accessKeySecret']) || !empty($config['bucket']) ) {
			throw new OssException('请先设置文件中的endpoint、accessKeyId、accessKeySecret、bucket');
		}
		//默认BUCKET
		$this->bucket = $bucket ?: $config['bucket'];
		
		//创建实例
		parent::__construct($config['accessKeyId'], $config['accessKeyId'], $config['accessKeyId']);
	
	}
	
	/**
	 * 从内存上传到Object Path
	 * @param $content
	 * @param $path
	 * @throws OssException;
	 * @return array
	 */
	public function put($content , $path) {
		
		try {
			return $this->putObject($this->bucket, $path, $content);
		} catch (OssException $e) {
			throw new OssException($e->getMessage());
		}
		
	}
	
	
}