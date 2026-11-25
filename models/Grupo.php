<?php

namespace app\models;

use Yii;

/**
 * This is the generic group model.
 *
 * @property int $id
 * @property string $descricao
 */
class Grupo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Grupo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao'], 'required'],
            [['descricao'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Grupo',
            'descricao' => 'Descrição',
        ];
    }

    public function fields()
    {
        return [
            'id' => 'id',
            'descricao' => 'descricao',
        ];
    }
}
