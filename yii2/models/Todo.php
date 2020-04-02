<?php


namespace app\models;


use yii\db\ActiveRecord;

class Todo extends ActiveRecord
{
    public static function tableName()
    {
        return 'todos';
    }

    public function category()
    {
        return $this->hasOne(Category::class, ['category_id' => 'id']);
    }

    public function rules()
    {
        return [
            [['title', 'done'], 'string', 'min' => 1],
            ['done', 'boolean'],
        ];
    }
}
