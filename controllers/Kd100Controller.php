<?php

namespace yii\express\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\express\models\Express;

class Kd100Controller extends Controller{

	public $enableCsrfValidation = false;

	public function behaviors(){
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'notify' => ['post'],
				],
			],
		];
	}

	public function actionNotify($id){
		$response = ['result' => 'false', 'returnCode' => '500', 'message' => '失败'];

		$express = Express::findOne($id);
		if($express && isset($_POST['param'])){
			$param = Json::decode($_POST['param']);
			if(isset($param['lastResult']) && $express->company == $param['lastResult']['com'] && $express->number == $param['lastResult']['nu'] && (empty($express->auth_key) || (isset($_POST['sign']) && $this->module->manager->verify($_POST['sign'], $_POST['param'], $express->auth_key)))){
				$express->status = $manager->getStatus($param['lastResult']['state']);
				$express->details = Json::encode($param['lastResult']['data']);
				if($param['lastResult']['ischeck'] == 1 && $param['lastResult']['state'] == 3){
					$express->receipted_at = strtotime($param['lastResult']['data'][0]['time']);
				}
				if($express->save()){
					$response['result'] = 'true';
					$response['returnCode'] = '200';
					$response['message'] = '成功';
				}
			}
		}

		\Yii::$app->response->format = 'json';
		return $response;
	}

}
