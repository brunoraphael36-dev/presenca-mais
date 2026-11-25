<?php

namespace app\services\interfaces;

use app\models\TokenAcesso;
use app\models\WS\Usuario;

interface IWebServiceAutenticadoService
{
    /**
     * Undocumented function
     *
     * @param [type] $email
     * @param [type] $senha
     * @return TokenAcesso
     */
    public function getToken($email = null, $senha = null);
}
