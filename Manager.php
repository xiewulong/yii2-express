<?php
/*!
 * yii2 extension - 快递接口
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-express
 * https://raw.githubusercontent.com/xiewulong/yii2-express/master/LICENSE
 * create: 2015/4/28
 * update: 2015/5/16
 * version: 0.0.1
 */

namespace yii\express;

use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;
use yii\express\apis\Kd100;
use yii\express\models\Express;

class Manager{

	//回调路由
	public $callback;

	//快递100密钥
	public $key;
	
	//所支持的快递公司
	public $companies = [];

	//启用行政区域解析功能
	public $resultv2 = false;
	
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
		$express = new Express;
		$express->company = $company;
		$express->number = $number;
		$express->generateAuthKey();
		if($express->save()){
			$express->details = Kd100::sdk($this->key)->poll($company, $number, \Yii::$app->urlManager->createAbsoluteUrl([$this->callback, 'id' => $express->id]), $express->auth_key, $this->resultv2);
			if($express->save()){
				$result = Json::decode($express->details);
				return isset($result['returnCode']) && $result['returnCode'] == 200 ? $express->id : 0;
			}
		}

		return 0;
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
