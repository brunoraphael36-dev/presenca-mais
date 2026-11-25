<?php

namespace app\models;

use Yii;

/**
 * This is the generic participant model.
 *
 * @property int $id
 * @property string $nome
 * @property string $matricula
 * @property int $grupoId
 * @property int $idade
 */
class Participante extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Participante';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome' ,'matricula', 'grupoId', 'idade'], 'required'],
            [['nome'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Participante ID',
            'nome' => 'Nome',
            'matricula' => 'Registro',
            'grupoId' => 'Grupo',
            'idade' => 'Idade',
        ];
    }

    public function getGrupo()
    {
        return $this->hasOne(Grupo::class, ['id' => 'grupoId']);
    }

    public function fields()
    {
        return [
            'id' => 'id',
            'nome' => 'nome',
            'matricula' => 'matricula',
            'grupoId' => 'grupoId',
            'idade' => 'idade',
        ];
    }
}

