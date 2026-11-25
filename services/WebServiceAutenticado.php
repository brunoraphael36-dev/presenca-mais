<?php

namespace app\services;


use app\models\TokenAcesso;
use app\services\interfaces\IAuthorizationService;

class WebServiceAutenticado extends BaseWebService
{
    /**
     * @var TokenAcesso
     */
    public $token;

    /**
     * Usuário usado na autenticação para se conseguir o token de acesso. Caso esteja nulo, o usuário padrão da aplicação
     * será usado.
     * @var string
     */
    public $usuario;

    /**
     * Senha usada na autenticação para se conseguir o token de acesso. Caso esteja nulo, uma senha padrão da aplicação
     * será usada.
     * @var string
     */
    public $senha;

    public function init()
    {
        if ($this->token === null) {
            $token = $this->getToken($this->usuario, $this->senha);
            $token = json_decode($token);

            $this->token = new TokenAcesso();
            $this->token->access_token = $token->access_token;
            $this->token->token_type = $token->token_type;
        }
    }

    /**
     * @param string $email
     * @param string $password
     * @return TokenAcesso
     */
    protected function getToken($email = null, $password = null)
    {
        $authParams = \Yii::$app->params['webservice']['autorizacao'];
        $authParams['email'] = is_null($email) ? $authParams['email'] : $email;
        $authParams['password'] = is_null($password) ? $authParams['password'] : $password;

        $service = \Yii::$container->get(IAuthorizationService::class);
        return $service->getToken($authParams);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $config
     * @param null $returnType
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonMapper_Exception
     */
    public function request($method, $uri, array $config = [], $returnType = null)
    {
        $config['headers']['Content-type'] = 'application/json';
        $config['headers']['Authorization'] = $this->token->token_type . ' ' . $this->token->access_token;

        return parent::request($method, $uri, $config, $returnType);
    }
}
