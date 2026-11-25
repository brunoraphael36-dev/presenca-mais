<?php

namespace app\services\interfaces;

use app\models\WS\Usuario;

interface IAutenticationService
{
    /**
     * @param string $email
     * @return string Hash de ativação
     */
    public function getHashAtivacao($email);

    /**
     * @param string $email
     * @param string $hash
     * @return boolean
     */
    public function validaHash($email, $hash);

    /**
     * @param string $email
     * @param string $senha
     * @return boolean
     */
    public function atualizaSenha($email, $senha);

    /**
     * @param string $email
     * @param string $senha
     * @return string
     */
    public function getToken($email, $senha);

    /**
     * @param string $token
     * @return boolean
     */
    public function TokenValidate($token);

    /**
     * @param string $email
     * @return \app\models\WS\Usuario[]
     */
    public function ListarUsuariosPorEmail($email);

    /**
     * @param string $nome
     * @return \app\models\WS\Usuario[]
     */
    public function ListarUsuariosPorNome($nome);

    /**
     * @param string $email
     * @return \app\models\WS\Usuario|boolean
     */
    public function GetUsuarioByEmail($email);

    /**
     * @param string $email
     * @return \app\models\WS\Usuario[]
     */
    public function GetUsuariosByEmail($email);

    /**
     * @param integer $id
     * @return \app\models\WS\Usuario
     */
    public function GetUsuarioById($id);

    /**
     * @param integer $id
     * @param string $email
     * @return boolean
     */
    public function atualizaCodigo($id, $email);

    /**
     * @param string $id
     * @param string $email
     * @return boolean
     */
    public function atualizaCodigoCFO($id, $email);

    /**
     * @param string $email
     * @return \app\models\WS\Permissoes[]
     */
    public function getFuncoesByEmail($email);

    /**
     * @param string $token
     * @return Usuario
     */
    public function GetUsuarioByToken($token);
}
