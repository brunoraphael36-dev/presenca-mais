<?php

$envFile = dirname(__DIR__) . '/.env';

if (is_file($envFile)) {
    (new \Symfony\Component\Dotenv\Dotenv())->load($envFile);
}
