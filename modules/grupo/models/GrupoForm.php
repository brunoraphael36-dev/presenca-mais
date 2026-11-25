<?php

namespace app\modules\grupo\models;

use app\models\Grupo;
use yii\base\Model;
use yii\web\HttpException;

class GrupoForm extends Model
{
    public $id;

    public $descricao;

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

    /**
     * @return bool
     * @throws HttpException
     */
    public function Save()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $grupo = new Grupo();

            $grupo->descricao = $this->descricao;

            if (!$grupo->save()) {
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
            $grupo = Grupo::findOne(['id' => $this->id]);

            $grupo->descricao = $this->descricao;

            if (!$grupo->save()) {
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
        $model = Grupo::findOne($id);
        if (empty($model)) {
            throw new \UnexpectedValueException("Grupo não encontrado.");
        }
        $this->attributes = $model->attributes;

        return $this;
    }
}
