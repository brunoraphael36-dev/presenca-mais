<?php

namespace app\services;

use app\models\TokenAcesso;
use app\services\interfaces\IAuthorizationService;
use GuzzleHttp\Client;
use Yii;

class AuthorizationService extends FBServices implements IAuthorizationService
{
    /**
     * @param array $authParams
     * @return TokenAcesso
     */
    public function getToken($authParams = [])
    {
        return $this->requestByPost('api/v1/portalfbuni/admin/login', $authParams, TokenAcesso::class);
    }
}
