<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m180205_093918_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'nickname'=>$this->string(50)->notNull()->defaultValue('0')->comment('用户昵称'),
            'avatar'=>$this->string(255)->notNull()->defaultValue('0')->comment('用户头像'),
            'real_name'=>$this->string(50)->notNull()->defaultValue('0')->comment('用户真实姓名'),
            'sex'=>$this->smallInteger(1)->notNull()->defaultValue(1)->comment('用户性别 1：男 2：女 3:保密'),
            'tel'=>$this->char(11)->notNull()->defaultValue('0')->comment('用户手机号'),
            'access_token'=>$this->string(255)->notNull()->comment('用户token'),
            'expire_time'=>$this->bigInteger(13)->notNull()->comment('Token过期时间'),
            'created'=>$this->dateTime()->notNull(),
            'updated'=>$this->timestamp(),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
