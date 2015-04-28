<?php

use yii\db\Schema;
use yii\db\Migration;

class m150109_010000_express extends Migration{

	public function up(){
		$tableOptions = 'engine=innodb character set utf8';
		if($this->db->driverName === 'mysql') {
			$tableOptions .= ' collate utf8_unicode_ci';
		}

		$this->createTable('{{%express}}', [
			'id' => Schema::TYPE_PK . ' comment "快递单id"',
			'mode' => Schema::TYPE_STRING . '(50) not null comment "方式"',
			'name' => Schema::TYPE_STRING . '(50) comment "名称"',
			'number' => Schema::TYPE_STRING . ' not null comment "快递单号"',
			'created_at' => Schema::TYPE_INTEGER . ' not null comment "创建时间"',
			'updated_at' => Schema::TYPE_INTEGER . ' not null comment "更新时间"',
		], $tableOptions . ' comment="快递信息"');
	}

	public function down(){
		$this->dropTable('{{%express}}');
	}

}
