<?php

namespace app\modules\participante\controllers;

use app\controllers\WebController;
use app\models\Participante;
use app\models\Grupo;
use app\modules\participante\models\ParticipanteForm;
use app\modules\participante\models\ParticipanteSearch;
use yii\filters\AccessControl;
use Yii;

class DefaultController extends WebController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            /** @var \app\models\User $user */
                            $user = Yii::$app->user->identity;
                            return $user && $user->hasPermission('participante.access');
                        },
                    ],
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        $participantes = new ParticipanteSearch();
        $dataProvider = $participantes->search(\Yii::$app->request->queryParams);

        $grupos = Grupo::find()->orderBy(['descricao' => SORT_ASC])->all();

        return $this->render('index', [
            'searchModel' => $participantes,
            'dataProvider' => $dataProvider,
            'grupos' => $grupos,
        ]);
    }

    public function actionCriar()
    {
        /** @var ParticipanteForm $model */
        $model = new ParticipanteForm();

        $grupos = Grupo::find()->orderBy(['descricao' => SORT_ASC])->all();
        $post = \Yii::$app->request->post();

        if ($model->load($post)) {

            $model->matricula = trim((string)$model->matricula);

            $existeDuplicado = Participante::find()
                ->where(['matricula' => $model->matricula])
                ->exists();

            if ($existeDuplicado) {
                $model->addError('matricula', 'Já existe um participante com esta matrícula.');
            } else {
                if ($model->validate() && $model->Save()) {
                    \Yii::$app->session->setFlash('success', 'Participante criado com sucesso.');
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('form', [
            'model' => $model,
            'grupos' => $grupos,
        ]);
    }

    public function actionEditar($id)
    {
        $model = new ParticipanteForm();
        $model->setId($id);

        $grupos = Grupo::find()->orderBy(['descricao' => SORT_ASC])->all();

        $post = \Yii::$app->request->post();

        if ($model->load($post)) {
            if ($model->Update()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('form', [
            'grupos' => $grupos,
            'model' => $model,
        ]);
    }

    public function actionExcluir($id)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $model = Participante::findOne($id);
            if (!$model->delete()) {
                throw new \Exception();
            }

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
        }

        return $this->redirect(['index']);
    }
}
