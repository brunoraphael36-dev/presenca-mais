<?php

namespace app\modules\user;

use yii\base\Module;

class UserModule extends Module
{
    public $layout = '/login';
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\user\controllers';

    public function init()
    {
        parent::init();
    }
}