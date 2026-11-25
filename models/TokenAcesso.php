<?php

namespace app\models;

use app\services\AutorizacaoService;
use yii\base\Model;
use yii\web\IdentityInterface;

class TokenAcesso extends Model
{
    public $id;
    public $access_token;
    public $token_type;
    public $expires_in;
    public $audience;
}
