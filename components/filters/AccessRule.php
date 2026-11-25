<?php

namespace app\components\filters;

use Yii;

class AccessRule
{
    public static function defineAccess($permission)
    {
        foreach (\Yii::$app->user->identity->permissoes as $acesso) {
            if ($acesso->funcaoNome == \Yii::$app->params['access'][$permission]) {
                return true;
            }
        }

        return false;
    }

}
