<?php

namespace Ittools\Smslabs;

use GuzzleHttp\Client;
use Ittools\Smslabs\Exception\InvalidResponseException;

class HttpClient
{
    const API_URL = 'https://api.smslabs.net.pl/apiSms';

    /**
     * @var string
     */
    private $appKey = null;

    /**
     * @var string
     */
    private $secretKey = null;

    /**
     * @var Client
     */
    private $client = null;

    /**
     * HttpClient constructor.
     * @param string $appKey
     * @param string $secretKey
     */
    public function __construct($appKey, $secretKey)
    {
        $this->appKey    = $appKey;
        $this->secretKey = $secretKey;
    }

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @return \stdClass
     * @throws InvalidResponseException
     */
    public function sendRequest($url, $data = null, $method = 'GET')
    {
        $this->validateMethod($method);

        $response = $this->getClient()->request($method, self::API_URL.$url, [
            'auth' => [$this->appKey, $this->secretKey],
            'form_params' => $data,
        ]);

        if ($response->getStatusCode() != 200) {
            throw new InvalidResponseException();
        }

        $bodyJson = (string)$response->getBody();

        $bodyObj = \GuzzleHttp\json_decode($bodyJson);

        if (!property_exists($bodyObj, 'data')) {
            throw new InvalidResponseException('Missing data property in response');
        }

        return $bodyObj->data;
    }

    private function validateMethod($method)
    {
        if (!in_array($method, ['POST', 'PUT', 'GET', 'DELETE'])) {
            throw new \InvalidArgumentException('Invalid method');
        }

        return true;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }
}
