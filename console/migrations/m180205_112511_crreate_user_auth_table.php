<?php

use yii\db\Migration;

/**
 * Class m180205_112511_crreate_user_auth_table
 */
class m180205_112511_crreate_user_auth_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_auth}}', [
            'id' => $this->primaryKey(),
            'uid'=>$this->integer(11)->notNull()->comment('用户uid'),
            'identity_type'=>$this->smallInteger(1)->notNull()->comment('登录类型 ( 1:手机号 2:账户密码 3:微信 4:微博 5: qq )'),
            'identity_account'=>$this->string(255)->notNull()->comment('登录账号( 手机号/ 账号/ 微信unionid/微信openid/微博token/qqtoken )'),
            'credential'=>$this->string(255)->comment('凭据 (密码/openid/....)'),
            'created'=>$this->dateTime()->notNull(),
            'updated'=>$this->timestamp(),
        ],$tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%user_auth}}');
    }
}
