<?php

namespace app\controllers;

use yii\web\Controller;

class WebController extends Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        return true;
    }
}
