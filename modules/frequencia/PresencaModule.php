<?php

namespace app\modules\frequencia;

/**
 * presencaModules module definition class
 */
class PresencaModule extends \yii\base\Module
{
    public $layout = '/dashboard';

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\frequencia\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here+
    }
}
