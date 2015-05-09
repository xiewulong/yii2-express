<?php

namespace yii\express\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;

class Express extends ActiveRecord{

	public static function tableName(){
		return '{{%express}}';
	}

	public function behaviors(){
		return [
			TimestampBehavior::className(),
		];
	}

	/**
	 * 生成授权key
	 * @method generateAuthKey
	 * @return {none}
	 * @example $this->generateAuthKey();
	 */
    public function generateAuthKey(){
		$this->auth_key = \Yii::$app->security->generateRandomString();
	}

}
