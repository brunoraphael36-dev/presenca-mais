<?php
namespace app\services\interfaces;

interface IHttpClient
{
    /**
     * @param string $method
     * @param string $uri
     * @param array $config
     * @return mixed
     */
    public function request($method, $uri, array $config = []);
}
