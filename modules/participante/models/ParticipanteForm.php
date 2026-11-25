<?php

namespace app\modules\participante\models;

use app\models\Participante;
use yii\base\Model;
use yii\web\HttpException;

class ParticipanteForm extends Model
{
    public $id;

    public $nome;

    public $matricula;

    public $grupoId;

    public $idade;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'matricula', 'grupoId', 'idade'], 'required'],
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

    /**
     * @return bool
     * @throws HttpException
     */
    public function Save()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $participante = new Participante();

            $participante->nome = $this->nome;
            $participante->matricula = $this->matricula;
            $participante->grupoId = $this->grupoId;
            $participante->idade = $this->idade;

            if (!$participante->save()) {
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
            $participante = Participante::findOne(['id' => $this->id]);

            $participante->nome = $this->nome;
            $participante->matricula = $this->matricula;
            $participante->grupoId = $this->grupoId;
            $participante->idade = $this->idade;

            if (!$participante->save()) {
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
        $model = Participante::findOne($id);
        if (empty($model)) {
            throw new \UnexpectedValueException("Participante não encontrado.");
        }
        $this->attributes = $model->attributes;

        return $this;
    }
}


