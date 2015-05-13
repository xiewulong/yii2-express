<?php

namespace yii\express\models;

interface ExpressInterface{

	/**
	 * 已签收 - 后台通知调用
	 * @method receipted
	 * @since 0.0.1
	 * @param {string} $id 快递记录id
	 * @return {none}
	 * @example static::receipted
	 */
	public static function receipted($id);

}
