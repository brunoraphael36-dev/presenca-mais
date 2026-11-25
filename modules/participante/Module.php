<?php

namespace app\modules\participante;

/**
 * participante module definition class
 */
class Module extends \yii\base\Module
{
    public $layout = '/dashboard';

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\participante\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here+
    }
}
