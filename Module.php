<?php

namespace yii\express;

use Yii;
use yii\express\models\Express;

class Module extends \yii\base\Module{

	public $defaultRoute = 'express';

	public $defaultComponent = 'express';

	public $manager;

	//异步通知内部调用类
	public $asyncClass;

	//同步通知内部调用路由
	public $syncRoute;

	public function init(){
		parent::init();

		$this->manager = \Yii::createObject(Yii::$app->components[$this->defaultComponent]);
	}

}
