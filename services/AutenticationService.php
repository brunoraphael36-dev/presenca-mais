<?php

namespace app\services;

use app\models\WS\Permissoes;
use app\models\WS\Usuario;
use app\services\interfaces\IAutenticationService;
use Yii;

class AutenticationService implements IAutenticationService
{
    public function getHashAtivacao($email)
    {
        return $this->requestByPost(
            'FBAutentica/Usuario/EsqueciMinhaSenha',
            ['Email' => $email]
        );
    }

    public function validaHash($email, $hash)
    {
        return $this->requestByPost(
            'FBAutentica/Usuario/ValidarHash',
            ['Email' => $email, 'HashAtivacao' => $hash]
        );
    }

    public function atualizaSenha($email, $senha)
    {
        return $this->requestByPost(
            'FBAutentica/Usuario/AtualizaSenha',
            ['Email' => $email, 'Senha' => base64_encode($senha)]
        );
    }

    public function getToken($email, $senha)
    {
        $hash = base64_encode($email . " " . $senha);

        return $this->requestByGet(
            'FBAutentica/Usuario/GetToken',
            ['Hash' => $hash]
        );
    }

    public function TokenValidate($token)
    {
        return $this->requestByGet(
            'FBAutentica/Usuario/TokenValidate',
            ['Token' => $token]
        );
    }

    public function ListarUsuariosPorEmail($email)
    {
        return $this->requestByGet(
            'FBAutentica/Usuario/ListarUsuariosPorEmail',
            ['Email' => $email],
            Usuario::class
        );
    }

    public function ListarUsuariosPorNome($nome)
    {
        return $this->requestByGet(
            'FBAutentica/Usuario/ListarUsuariosPorNome',
            ['nome' => $nome],
            Usuario::class
        );
    }

    public function GetUsuarioByEmail($email)
    {
        return $this->requestByGet(
            'FBAutentica/Usuario/GetUsuarioByEmail',
            ['Email' => $email],
            Usuario::class
        );
    }

    public function GetUsuariosByEmail($email)
    {
        return $this->requestByGet(
            'FBAutentica/Usuario/GetUsuariosByEmail',
            ['Email' => $email],
            Usuario::class
        );
    }

    public function GetUsuarioById($id)
    {
        return $this->requestByGet(
            'FBAutentica/Usuario/GetUsuarioComInativo',
            ['id' => $id],
            Usuario::class
        );
    }

    public function atualizaCodigo($id, $email)
    {
        return $this->requestByPost(
            'FBAutentica/Usuario/AtualizaCodigo',
            ['Email' => $email, 'CodigoPessoa' => $id]
        );
    }

    public function atualizaCodigoCFO($id, $email)
    {
        return $this->requestByPost(
            'FBAutentica/Usuario/AtualizaCodigoCFO',
            ['Email' => $email, 'CodigoCFO' => $id]
        );
    }


    public function getFuncoesByEmail($email)
    {
        return $this->requestByGet(
            'FBAutentica/Usuario/GetFuncaoByEmail',
            [
                'email' => $email,
                'sistemaLogado' =>Yii::$app->params['sistema-logado']
            ],
            Permissoes::class
        );
    }

    public function GetUsuarioByToken($token)
    {
        return $this->requestByGet(
            'FBAutentica/Usuario/GetUsuarioByToken',
            ['token' => $token],
            Usuario::class
        );
    }
}
