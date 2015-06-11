<?php
/*!
 * yii2 extension - 快递接口
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-express
 * https://raw.githubusercontent.com/xiewulong/yii2-express/master/LICENSE
 * create: 2015/4/28
 * update: 2015/6/11
 * version: 0.0.1
 */

namespace yii\express;

use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;
use yii\express\apis\Kd100;
use yii\express\models\Express;

class Manager{

	//通知地址的协议类型, 'http'或'https'
	public $protocol = null;

	//回调路由
	public $callback;

	//快递100密钥
	public $key;
	
	//所支持的快递公司
	public $companies = [];

	//启用行政区域解析功能
	public $resultv2 = false;

	//debug模式
	public $debug = false;
	
	//快递状态列表
	private $statuses = [];

	/**
	 * 验证数据可靠性
	 * @method verify
	 * @since 0.0.1
	 * @param {string} $sign 签名字符串
	 * @param {string} $param 数据
	 * @param {string} $salt 签名用随机字符串
	 * @return {boolean}
	 * @example Yii::$app->express->verify($sign, $param, $salt);
	 */
	public function verify($sign, $param, $salt){
		return Kd100::sdk($this->key)->verify($sign, $param, $salt);
	}

	/**
	 * 创建快递跟踪信息
	 * @method send
	 * @since 0.0.1
	 * @param {string} $company 快递公司代码
	 * @param {string} $number 快递单号
	 * @return {boolean}
	 * @example Yii::$app->express->send($company, $number);
	 */
	public function send($company, $number){
		$eid = 0;
		$express = new Express;
		$express->company = $company;
		$express->number = $number;
		$express->generateAuthKey();
		if($express->save()){
			$result = $this->debug ? ['returnCode' => 200] : Json::decode(Kd100::sdk($this->key)->poll($company, $number, \Yii::$app->urlManager->createAbsoluteUrl([$this->callback, 'id' => $express->id], $this->protocol), $express->auth_key, $this->resultv2));
			if(isset($result['returnCode'])){
				switch($result['returnCode']){
					case 200:
						$express->status = '提交成功';
						$eid = $express->id;
						break;
					case 501:
						$express->status = '重复订阅';
						break;
				}
				$express->save();
			}
		}

		return $eid;
	}

	/**
	 * 快递公司名称
	 * @method getCompany
	 * @since 0.0.1
	 * @param {string} $code 快递公司代码
	 * @return {string}
	 * @example Yii::$app->express->getCompany();
	 */
	public function getCompany($code){
		$companies = $this->getCompanies();
		
		return isset($companies[$code]) ? $companies[$code] : null;
	}

	/**
	 * 获取所支持的快递公司列表
	 * @method getCompanies
	 * @since 0.0.1
	 * @return {array}
	 * @example Yii::$app->express->getCompanies();
	 */
	public function getCompanies(){
		if(empty($this->companies)){
			$this->companies = require(__DIR__ . '/companies.php');
		}

		return $this->companies;
	}

	/**
	 * 获取状态信息
	 * @method getStatus
	 * @since 0.0.1
	 * @param {string} $code 状态码
	 * @return {string}
	 * @example Yii::$app->express->getStatus($code);
	 */
	public function getStatus($code){
		if(empty($this->statuses)){
			$this->statuses = require(__DIR__ . '/statuses.php');
		}

		return isset($this->statuses[$code]) ? $this->statuses[$code] : null;
	}

}
