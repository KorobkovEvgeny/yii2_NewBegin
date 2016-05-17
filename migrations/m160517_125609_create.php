<?php

use yii\db\Migration;

class m160517_125609_create extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            "id"=>  $this->primaryKey(),
            "username" => $this->string(128)->notNull(),
            "name" => $this->string(45),
            "surname" => $this->string(45),
            "password" => $this->string(255)->notNull(),
            "salt" => $this->string(255)->notNull(),
            "access_token" => $this->string(255)->notNull(),
            "create_date" => $this->timestamp()->notNull()
        ]);  
                
    }

    public function down()
    {
        echo "m160517_125609_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
