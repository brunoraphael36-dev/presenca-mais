<?php

namespace app\modules\grupo;

/**
 * Grupo module definition class
 */
class Module extends \yii\base\Module
{
    public $layout = '/dashboard';

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\grupo\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here+
    }
}
