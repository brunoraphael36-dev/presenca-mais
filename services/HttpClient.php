<?php

namespace app\services;

use app\services\interfaces\IHttpClient;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use yii\base\Component;

class HttpClient extends Component implements IHttpClient
{
    const RETORNO_JSON = 'json';
    const RETORNO_XML = 'xml';
    const RETORNO_SOAP = 'soap';
    const RETORNO_REQUEST = 'none';

    /** @var ClientInterface */
    protected $_client;

    public $retornoTipo = self::RETORNO_JSON;

    public function __construct(BaseHttpClient $client, $config = [])
    {
        $this->_client = $client->getClient();
        parent::__construct($config);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $config
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, array $config = [])
    {
        /** @var ResponseInterface $return */
        $return = $this->_client->request($method, $uri, $config);
        return $this->prepareRetorno($return);
    }

    /**
     * @param ResponseInterface $retorno
     *
     * @return mixed
     */
    public function prepareRetorno($retorno)
    {
        $formatedReturn = null;
        switch ($this->retornoTipo) {
            case $this->retornoTipo == self::RETORNO_REQUEST:
                $formatedReturn = $retorno;
                break;
            case $this->retornoTipo == self::RETORNO_JSON:
                $formatedReturn = $this->retornoJson($retorno);
                break;
            case $this->retornoTipo == self::RETORNO_XML:
                $formatedReturn = $this->retornoXml($retorno);
                break;
            case $this->retornoTipo == self::RETORNO_SOAP:
                $formatedReturn = $this->retornoSoap($retorno);
                break;
            default:
                $formatedReturn = $this->retornoJson($retorno);
                break;
        }
        return $formatedReturn;
    }

    /**
     * @param ResponseInterface $retorno
     *
     * @return object
     */
    public function retornoJson($retorno)
    {
        return json_decode($retorno->getBody());
    }

    /**
     * @param ResponseInterface $retorno
     *
     * @return mixed
     */
    public function retornoXml($retorno)
    {
        $return = new \stdClass();
        $xmlString = $retorno->getBody()->getContents();
        $xml = simplexml_load_string($xmlString);
        if ($xml->count() > 1) {
            foreach ($xml as $children) {
                $return->retorno[] = $children;
            }
        } else {
            $return->retorno = $xml;
        }

        return json_decode(json_encode($return));
    }

    public function retornoSoap($retorno)
    {
        $xmlString = $retorno->getBody()->getContents();
        // SimpleXML seems to have problems with the colon ":" in the <xxx:yyy> response tags, so take them out
        $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xmlString);
        $xml = simplexml_load_string($xml);
        return json_decode(json_encode($xml));
    }
}
