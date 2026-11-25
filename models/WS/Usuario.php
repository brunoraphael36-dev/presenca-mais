<?php

namespace app\models\WS;

use app\models\TokenAcesso;
use app\services\interfaces\IWebServiceAutenticadoService;
use yii\base\Model;
use yii\web\IdentityInterface;
use app\services\interfaces\IAutenticationService;
use Exception;
use Symfony\Component\EventDispatcher\Event;
use Yii;
use yii\filters\AccessControl;

class Usuario extends Model implements IdentityInterface
{
    const EVENT_AFTER_LOGIN = 'afterLogin';

    public $id;
    public $email;
    public $password;
    public $hash;
    public $usuario;
    /**
     *
     * @var TokenAcesso
     *
     */
    public $token;
    private $_autenticationService;
    public $auth_key;

    /**
     * @var Permissoes[]
     */
    public $permissoes;

    public function __construct(
        IAutenticationService $autenticationService = null,
        array $config = []
    ) {
        if ($autenticationService) {
            $this->_autenticationService = $autenticationService;
        } else {
            $this->_autenticationService = \Yii::$container->get(IAutenticationService::class);
        }

        parent::__construct($config);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return TokenAcesso
     * @throws \Exception
     */
    public static function getToken($email, $password)
    {
        /** @var IWebServiceAutenticadoService $webserviceautenticado */
        $webserviceautenticado = Yii::$container->get(IWebServiceAutenticadoService::class);

        return $webserviceautenticado->getToken($email, $password);
    }

    private function setFunctions()
    {
        try {
            /** @var IAutenticationService permissoes */
            $this->permissoes = $this->_autenticationService->getFuncoesByEmail($this->email);
        } catch (\Exception $ex) {
            $this->permissoes = [];
        }
    }

    public function montarUsuario()
    {
        $this->setFunctions();
    }

    /**
     * @param int $id
     * @param Usuario $representante
     *
     * @return Usuario
     */
    public static function rebuildIdentity($id, $representante = null)
    {
        $session = \Yii::$app->session;
        /** @var IAutenticationService $autenticationService */
        $autenticationService = \Yii::$container->get(IAutenticationService::class);
        $usuario = $autenticationService->GetUsuarioById($id);
        if ($usuario) {
            $usuario->montarUsuario();
            if ($representante) {
                $usuario->representante = call_user_func('get_object_vars', $representante);
            }
        }

        $user = call_user_func('get_object_vars', $usuario);
        $session->set('user', $user);
        return $usuario;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $session = \Yii::$app->session;
        if ($session->has('user')) {
            $user = new Usuario();
            $user->setAttributes($session->get('user'), false);
            return $user;
        }
        return self::rebuildIdentity($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        # code...
    }

    public static function findIdentityByEmail($email)
    {
        /** @var IAutenticationService $autenticationService */
        $autenticationService = \Yii::$container->get(IAutenticationService::class);

        if ($user = $autenticationService->GetUsuarioByEmail($email)) {
            $user->setFunctions();
            return $user;
        }

        return null;
    }

    public static function findUserByAccessToken($token)
    {
        /** @var IAutenticationService $autenticationService */
        $autenticationService = \Yii::$container->get(IAutenticationService::class);
        if ($user = $autenticationService->GetUsuarioByToken($token)) {
            return $user;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        //return $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @return bool
     */
    public static function getIsAdmin()
    {
        return in_array(Yii::$app->user->identity->email, Yii::$app->params['admins']);
    }
}
