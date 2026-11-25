<?php

namespace app\modules\user\controllers;

use app\models\LoginForm;
use yii\web\Controller;

class AutenticationController extends Controller
{
    public function actionLogin()
    {
        $this->layout = '/login';

        $post = \Yii::$app->request->post();

        $model = new LoginForm();

        if ($model->load($post) && $model->login()) {
            return $this->redirect(['/admin/banners/default/index']);
        }

        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(['/admin/login']);
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['/admin/login']);
    }
}