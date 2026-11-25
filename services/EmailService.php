<?php

namespace app\services;

use app\services\interfaces\IEmailService;
use PHPMailer\PHPMailer\PHPMailer;
use Yii;

class EmailService implements IEmailService
{
    /** @var PHPMailer */
    private $_mailer;

    public function __construct()
    {
        $this->_mailer = new PHPMailer(true);
        $this->_mailer->isSMTP();

        $this->_mailer->SMTPAuth = true;
        $this->_mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->_mailer->CharSet = 'UTF-8';

        $config = Yii::$app->params['phpMailerConfig'];
        $this->_mailer->Host = $config['host'];
        $this->_mailer->Username = $config['username'];
        $this->_mailer->Password = $config['password'];
        $this->_mailer->Port = $config['port'];

        $this->_mailer->addBCC('');
        $this->_mailer->isHTML(true);
    }

    public function setRemetente($mail, $name)
    {
        $this->_mailer->setFrom($mail, $name);
        return $this;
    }

    public function setDestinatario($destinatario)
    {
        $this->_mailer->addAddress($destinatario);
        return $this;
    }

    public function setAssunto($assunto)
    {
        $this->_mailer->Subject = $assunto;
        return $this;
    }

    public function setCorpo($corpo)
    {
        $this->_mailer->Body = $corpo;
        return $this;
    }

    public function enviar() : bool
    {
        return $this->_mailer->send();
    }
}
