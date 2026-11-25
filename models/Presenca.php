<?php

namespace app\models;

use Yii;

/**
 * This is the generic attendance model.
 *
 * @property int $id
 * @property string $matricula
 * @property string $data
 * @property int $presenca
 * @property string $justificativa
 * @property string $grupoId
 */
class Presenca extends \yii\db\ActiveRecord
{
    const PRESENTE = 'Presente';
    const FALTOU = 'Faltou';
    const FALTA_JUSTIFICADA = 'Falta Justificada';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Presenca';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['matricula', 'data', 'presenca'], 'required'],
            [['justificativa', 'grupoId'], 'safe'],
            [['justificativa'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Presença ID',
            'matricula' => 'Registro',
            'data' => 'Data',
            'presenca' => 'Presença',
            'justificativa' => 'Justificativa',
            'grupoId' => 'Grupo',
        ];
    }

    public function fields()
    {
        return [
            'id' => 'id',
            'matricula' => 'matricula',
            'data' => 'data',
            'presenca' => 'presenca',
            'justificativa' => 'justificativa',
            'grupoId' => 'grupoId',
        ];
    }

    /**
     * @property int|null $grupoId
     * @property \app\models\Grupo $grupo
     */
    public function getGrupo()
    {
        return $this->hasOne(\app\models\Grupo::class, ['id' => 'grupoId']);
    }
}
