<?php
/*!
 * yii2 extension - 快递接口 - 快递100 sdk
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-express
 * https://raw.githubusercontent.com/xiewulong/yii2-express/master/LICENSE
 * create: 2015/4/28
 * update: 2015/5/9
 * version: 0.0.1
 */

namespace yii\express\apis;

use Yii;
use yii\helpers\Json;

class Kd100{

	//扫码接口
	private $api = 'http://www.kuaidi100.com/poll';

	//配置参数
	private $key;

	/**
	 * 构造器
	 * @method __construct
	 * @since 0.0.1
	 * @param {string} $key 密钥
	 * @return {none}
	 */
	public function __construct($key){
		$this->key = $key;
	}

	/**
	 * 获取类对象
	 * @method sdk
	 * @since 0.0.1
	 * @param {string} $key 密钥
	 * @return {object}
	 * @example static::sdk($key);
	 */
	public static function sdk($key){
		return new static($key);
	}

	/**
	 * 验证数据可靠性
	 * @method verify
	 * @since 0.0.1
	 * @param {string} $sign 签名字符串
	 * @param {string} $param 数据
	 * @param {string} $salt 签名用随机字符串
	 * @return {boolean}
	 * @example $this->verify($sign, $param, $salt);
	 */
	public function verify($sign, $param, $salt){
		return \Yii::$app->security->compareString($sign, $this->sign($param, $salt));
	}

	/**
	 * 订阅请求
	 * @method poll
	 * @since 0.0.1
	 * @param {string} $company 订阅的快递公司的编码
	 * @param {string} $number 订阅的快递单号
	 * @param {string} $callbackurl 回调地址
	 * @param {string} [$salt=null] 签名用随机字符串
	 * @param {boolean} [$resultv2=false] 行政区域解析功能
	 * @param {string} [$schema=json] 传输格式
	 * @return {string}
	 * @example $this->poll($company, $number, $callbackurl, $salt, $resultv2);
	 */
	public function poll($company, $number, $callbackurl, $salt = null, $resultv2 = false, $schema = 'json'){
		$param = [
			'company' => $company,
			'number' => $number,
			'key' => $this->key,
			'parameters' => [
				'callbackurl' => $callbackurl,
			],
		];

		if(!empty($salt)){
			$param['parameters']['salt'] = $salt;
		}
		if($resultv2){
			$param['parameters']['resultv2'] = 1;
		}

		return $this->curl($this->api, http_build_query([
			'schema' => $schema,
			'param' => Json::encode($param),
		]));
	}

	/**
	 * 签名
	 * @method sign
	 * @since 1.0.0
	 * @param {string} $param 数据
	 * @param {string} $salt 签名用随机字符串
	 * @return {string}
	 */
	private function sign($param, $salt){
		return strtoupper(md5($param . $salt));
	}

	/**
	 * curl远程获取数据方法
	 * @method curl
	 * @since 1.0.0
	 * @param {string} $url 请求地址
	 * @param {array|string} [$data=null] post数据
	 * @param {string} [$useragent=null] 模拟浏览器用户代理信息
	 * @return {string} 返回获取的数据
	 */
	private function curl($url, $data = null, $useragent = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if(isset($data)){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		if(isset($useragent)){
			curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
		}
		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}

}
