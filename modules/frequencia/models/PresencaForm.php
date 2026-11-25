<?php

namespace app\modules\frequencia\models;

use app\models\Presenca;
use yii\base\Model;
use yii\web\HttpException;

class PresencaForm extends Model
{
    public $id;

    public $matricula;

    public $data;

    public $presenca;

    public $justificativa;

    public $grupoId;

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
            'id' => 'Registro de presença ID',
            'matricula' => 'Registro',
            'data' => 'Data',
            'presenca' => 'Presença',
            'justificativa' => 'Justificativa',
            'grupoId' => 'Grupo',
        ];
    }

    /**
     * @return bool
     * @throws HttpException
     */
    public function Save()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $presenca = new Presenca();

            $presenca->matricula = $this->matricula;
            $presenca->data = $this->data;
            $presenca->presenca = $this->presenca;
            $presenca->justificativa = $this->justificativa;
            $presenca->grupoId = $this->grupoId;

            if (!$presenca->save()) {
                throw new \Exception();
            }

            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new HttpException(500, $ex->getMessage());
        }
    }

    public function Update()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $presenca = Presenca::findOne(['id' => $this->id]);

            $presenca->matricula = $this->matricula;
            $presenca->data = $this->data;
            $presenca->presenca = $this->presenca;
            $presenca->justificativa = $this->justificativa;
            $presenca->grupoId = $this->grupoId;

            if (!$presenca->save()) {
                throw new \Exception();
            }

            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new HttpException(500, $ex->getMessage());
        }
    }

    public function setId($id)
    {
        $this->id = $id;
        $model = Presenca::findOne($id);
        if (empty($model)) {
            throw new \UnexpectedValueException("Presença não encontrada.");
        }
        $this->attributes = $model->attributes;

        return $this;
    }
}

class FrequenciaForm extends PresencaForm
{
}
