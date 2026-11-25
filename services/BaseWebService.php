<?php

namespace app\services;

use app\exceptions\WebServiceException;
use app\models\Paginacao;
use app\models\ResultadoPaginado;
use app\services\interfaces\IHttpClient;
use GuzzleHttp\ClientInterface;
use yii\web\Response;

class BaseWebService
{
    /** @var ClientInterface */
    protected $_serviceClient;

    public function __construct(IHttpClient $httpClient)
    {
        $this->_serviceClient = $httpClient;
    }

    /**
     * @param string      $method
     * @param string      $uri
     * @param array       $config
     * @param string|null $returnType
     *
     * @return mixed
     * @throws WebServiceException
     * @throws \JsonMapper_Exception
     */
    public function request($method, $uri, array $config = [], $returnType = null)
    {
        try {
            $command = $this->_serviceClient->request($method, $uri, $config);
        } catch (\Exception $ex) {
            if (YII_ENV == YII_ENV_DEV) {
                throw new WebServiceException($ex->getMessage(), 500, $ex);
            }
            throw new WebServiceException("Erro interno no servidor, tente novamente mais tarde.", 500, $ex);
        }
        if (!$command) {
            return null;
        }
        if (property_exists($command, "exception") && $command->exception) {
            \Yii::$app->response->format = Response::FORMAT_HTML;
            throw new WebServiceException($command->exception->errorMessage, $command->exception->errorCode);
        }

        $retorno = null;
        if (property_exists($command, "retorno")) {
            $retorno = $this->prepareReturn($command->retorno, $returnType);

            if (property_exists($command, "paginacao") && $command->paginacao) {
                $paginacao = $this->prepareReturn($command->paginacao, Paginacao::class);
                return new ResultadoPaginado([
                    'paginacao' => $paginacao,
                    'retorno' => $retorno,
                ]);
            }

            return $retorno;
        }

        return $this->prepareReturn($command, $returnType);
    }

    /**
     * @param string      $uri
     * @param array       $params
     * @param string|null $returnType
     * @param array       $headers
     *
     * @return mixed
     * @throws WebServiceException
     * @throws \JsonMapper_Exception
     */
    public function requestByGet($uri, array $params = [], $returnType = null, $headers = [])
    {
        $command = $this->request(
            'GET',
            $uri,
            ['query' => $params, 'headers' => $headers],
            $returnType
        );
        return $command;
    }

    /**
     * @param string      $uri
     * @param array       $params
     * @param string|null $returnType
     * @param array       $headers
     *
     * @return mixed
     * @throws WebServiceException
     * @throws \JsonMapper_Exception
     */
    public function requestByPost($uri, array $params = [], $returnType = null, $headers = [])
    {
        $command = $this->request(
            'POST',
            $uri,
            ['form_params' => $params, 'headers' => $headers],
            $returnType
        );
        return $command;
    }

    public function requestMultipart($uri, array $params = [], $returnType = null)
    {
        $command = $this->request(
            'POST',
            $uri,
            ['multipart' => $params],
            $returnType
        );
        return $command;
    }

    public function requestDelete($uri, array $params = [], $returnType = null)
    {
        $command = $this->request(
            'DELETE',
            $uri,
            ['form_params' => $params],
            $returnType
        );
        return $command;
    }

    /**
     * @param mixed       $return
     * @param string|null $returnType
     *
     * @return mixed
     * @throws \JsonMapper_Exception
     */
    private function prepareReturn($return, $returnType = null)
    {
        if ($returnType) {
            return $this->mapperReturn($return, $returnType);
        }
        return $return;
    }

    /**
     * @param mixed $return
     * @param string $returnType
     * @return mixed
     * @throws \JsonMapper_Exception
     */
    private function mapperReturn($return, $returnType)
    {
        $mapper = new \JsonMapper();
        $mapper->bStrictNullTypes = false;
        if (is_array($return)) {
            return $mapper->mapArray(
                $return,
                [],
                $returnType
            );
        } elseif (is_object($return)) {
            return $mapper->map($return, new $returnType());
        } else {
            return $return;
        }
    }
}
