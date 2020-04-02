<?php

use yii\db\Migration;

/**
 * Class m200401_230723_todo
 */
class m200401_230723_todo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('categories', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
        ]);
        $this->createTable('todos', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'category_id' => $this->integer(),
            'done' => $this->boolean(),
        ]);
        $this->addForeignKey('todo_cat', 'todos', 'category_id', 'categories', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('todos');
        $this->dropTable('categories');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200401_230723_todo cannot be reverted.\n";

        return false;
    }
    */
}
