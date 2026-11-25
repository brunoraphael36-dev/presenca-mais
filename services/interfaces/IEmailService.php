<?php

namespace app\services\interfaces;

interface IEmailService
{
    /**
     * Define o remetente do e-mail.
     *
     * @param string $mail O endereço de e-mail do remetente.
     * @param string $name O nome do remetente.
     * @return self
     */
    public function setRemetente(string $mail, string $name);

    /**
     * Define o destinatário do e-mail.
     *
     * @param string $destinatario O endereço de e-mail do destinatário.
     * @return self
     */
    public function setDestinatario(string $destinatario);

    /**
     * Define o assunto do e-mail.
     *
     * @param string $assunto O assunto do e-mail.
     * @return self
     */
    public function setAssunto(string $assunto);

    /**
     * Define o corpo do e-mail.
     *
     * @param string $corpo O conteúdo HTML ou texto simples do e-mail.
     * @return self
     */
    public function setCorpo(string $corpo);

    /**
     * Envia o e-mail configurado.
     *
     * @return bool Retorna true se o e-mail foi enviado com sucesso, false caso contrário.
     */
    public function enviar(): bool;
}
