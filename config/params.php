<?php

use PHPMailer\PHPMailer\PHPMailer;

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'webservice' => [
        'autorizacao' => [
            'email' => '',
            'password' => '',
            'grant_type' => 'password',
            'client_id' => 'c'
        ]
    ],

    'access' => [
        'acesso-basico' => '092020001',
    ],
    'sistema-logado' => 92,
    'phpMailerConfig' => [
        'host' => 'smtp.office365.com',
        'username' => '',
        'password' => '',
        'port' => 587,
    ],
];
