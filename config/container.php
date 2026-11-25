<?php

Yii::$container->set(\app\services\interfaces\IWebServiceAutenticadoService::class, app\services\WebServiceAutenticadoService::class);
Yii::$container->set(\app\services\interfaces\IHttpClient::class, \app\services\HttpClient::class);
Yii::$container->set(\app\services\interfaces\IAuthorizationService::class, \app\services\AuthorizationService::class);
Yii::$container->set(\app\services\interfaces\IAutenticationService::class, app\services\AutenticationService::class);
Yii::$container->set(\app\services\interfaces\IEmailService::class, app\services\EmailService::class);