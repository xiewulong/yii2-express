<?php

namespace yii\express;

use Yii;
use yii\express\models\Express;

class Module extends \yii\base\Module{

	public $defaultRoute = 'express';

	public $defaultComponent = 'express';

	public $manager;

	public function init(){
		parent::init();

		$this->manager = \Yii::createObject(Yii::$app->components[$this->defaultComponent]);
	}

}
