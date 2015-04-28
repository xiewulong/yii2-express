<?php

namespace yii\express\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;

class Express extends ActiveRecord{

	private $mac_hach = 'sha256';

	public static function tableName(){
		return '{{%payment}}';
	}

	public function behaviors(){
		return [
			TimestampBehavior::className(),
		];
	}

}
