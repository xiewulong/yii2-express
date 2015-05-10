<?php

use yii\db\Schema;
use yii\db\Migration;

class m150509_142253_express extends Migration{

	public function up(){
		$tableOptions = 'engine=innodb character set utf8';
		if($this->db->driverName === 'mysql') {
			$tableOptions .= ' collate utf8_unicode_ci';
		}

		$this->createTable('{{%express}}', [
			'id' => Schema::TYPE_PK . ' comment "id"',
			'company' => Schema::TYPE_STRING . '(50) not null comment "公司"',
			'number' => Schema::TYPE_STRING . ' not null comment "单号"',
			'auth_key' => Schema::TYPE_STRING . '(50) comment "授权密钥"',
			'status' => Schema::TYPE_STRING . '(50) comment "当前状态"',
			'details' => Schema::TYPE_TEXT . ' comment "详情"',
			'receipted_at' => Schema::TYPE_INTEGER . ' not null default 0 comment "签收时间"',
			'created_at' => Schema::TYPE_INTEGER . ' not null comment "创建时间"',
			'updated_at' => Schema::TYPE_INTEGER . ' not null comment "更新时间"',
		], $tableOptions . ' comment="快递记录"');
	}

	public function down(){
		$this->dropTable('{{%express}}');
	}

}
