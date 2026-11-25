<?php

namespace app\services;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class BaseHttpClient extends Client
{
    /**
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * BaseHttpClient constructor.
     * @param ClientInterface|null $client
     * @param array $config
     */
    public function __construct(ClientInterface $client = null, array $config = [])
    {
        $this->client = $client;
        parent::__construct($config);
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = new Client([
                'base_uri' => \Yii::$app->params['webservice']['baseUrl']
            ]);
        }

        return $this->client;
    }
}
