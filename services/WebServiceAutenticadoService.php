<?php

namespace app\services;

use app\models\TokenAcesso;
use app\models\WS\Usuario;
use app\services\interfaces\IAuthorizationService;
use app\services\interfaces\IWebServiceAutenticadoService;
use TheSeer\Tokenizer\Token;
use Yii;

class WebServiceAutenticadoService extends FBServices implements IWebServiceAutenticadoService
{
    protected $token;

    public function __construct()
    {
        $user = Yii::$app->user->identity;

        if (!$this->token && $user) {
            $this->token = $this->getToken($user->email, $user->password);
        }

        parent::__construct();
    }
    /**
     * Undocumented function
     *
     * @param [type] $email
     * @param [type] $password
     * @return TokenAcesso
     */
    public function getToken($email = null, $password = null)
    {
        /** @var Usuario $user */


        $user = \Yii::$app->user->identity;

        if (!empty($user) && isset($user->token)) {
            return $user->token;
        }

        $authParams = \Yii::$app->params['webservice']['autorizacao'];
        $authParams['email'] = is_null($email) ? $authParams['email'] : $email;
        $authParams['password'] = is_null($password) ? $authParams['password'] : $password;

        $autorization  = Yii::$container->get(IAuthorizationService::class);

        $tokenAccess = $autorization->getToken($authParams);

        $this->token = json_decode($tokenAccess);

        return $this->token;
    }

    public function request($method, $uri, array $config = [], $returnType = null)
    {
        $config['headers']['Authorization'] = $this->token->token_type . ' ' . $this->token->access_token;
        return parent::request($method, $uri, $config, $returnType);
    }
}
