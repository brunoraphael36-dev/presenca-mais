<?php
namespace app\exceptions;

use yii\base\UserException;

class WebServiceException extends UserException
{
    const BLOQUEIO = 1;
    const USUARIO_NAO_ENCONTRADO = 2;
    const ERRO_INTERNO_NO_SERVIDOR = 3;
    const NENHUM_RESULTADO_ENCONTRADO = 4;
    const INFORMACAO_INACESSIVEL = 5;
    const CONTEUDO_INDISPONIVEL = 6;

    public $statusCode;

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $this->getStatus($code);
        parent::__construct($message, $code, $previous);
    }

    public function getName()
    {
        return 'Aviso';
    }

    private function getStatus($code)
    {
        switch ($code):
            case $code == self::BLOQUEIO:
                $status = 401;
                break;
            case $code == self::USUARIO_NAO_ENCONTRADO:
                $status = 403;
                break;
            case $code == self::ERRO_INTERNO_NO_SERVIDOR:
                $status = 500;
                break;
            case $code == self::NENHUM_RESULTADO_ENCONTRADO:
                $status = 403;
                break;
            case $code == self::INFORMACAO_INACESSIVEL:
                $status = 403;
                break;
            case $code == self::CONTEUDO_INDISPONIVEL:
                $status = 403;
                break;
            default :
                $status = 500;
                break;
        endswitch;

        return $status;
    }
}
