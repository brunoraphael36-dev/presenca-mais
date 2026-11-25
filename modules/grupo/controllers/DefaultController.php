<?php

namespace app\modules\grupo\controllers;

use app\controllers\WebController;
use app\models\Grupo;
use app\modules\grupo\models\GrupoForm;
use app\modules\grupo\models\GrupoSearch;
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
                            return $user && $user->hasPermission('grupo.access');
                        },
                    ],
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        $grupos = new GrupoSearch();
        $dataProvider = $grupos->search(\Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $grupos,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCriar()
    {
        /** @var GrupoForm $model */
        $model = new GrupoForm();

        $post = \Yii::$app->request->post();

        if ($model->load($post)) {
            if ($model->Save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('form', [
            'model' => $model,
        ]);
    }

    public function actionEditar($id)
    {
        $model = new GrupoForm();
        $model->setId($id);

        $post = \Yii::$app->request->post();

        if ($model->load($post)) {
            if ($model->Update()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('form', [
            'model' => $model,
        ]);
    }

    public function actionExcluir($id)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $model = Grupo::findOne($id);
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
