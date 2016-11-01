<?php

namespace Ittoolspl\Smslabs;

use GuzzleHttp\Client;
use Ittoolspl\Smslabs\Exception\InvalidResponseException;

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
     * @return array
     * @throws InvalidResponseException
     */
    public function sendRequest($url, $data = null, $method = 'GET')
    {
        if ($this->validateHttpMethod($method) === false) {
            throw new \InvalidArgumentException('Invalid method');
        }

        $response = $this->getClient()->request($method, self::API_URL.$url, [
            'auth' => [$this->appKey, $this->secretKey],
            'form_params' => $data,
        ]);

        if ($response->getStatusCode() != 200) {
            throw new InvalidResponseException('Invalid HTTP code');
        }

        $bodyJson = (string)$response->getBody();

        return $this->parseJsonData($bodyJson);
    }

    /**
     * @param string $json
     * @return array
     * @throws InvalidResponseException
     */
    private function parseJsonData($json)
    {
        try {
            $bodyArr = \GuzzleHttp\json_decode($json, true);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidResponseException('Invalid JSON data');
        }

        if (!array_key_exists('data', $bodyArr)) {
            throw new InvalidResponseException('Missing data array key in response');
        }

        return $bodyArr['data'];
    }

    /**
     * @param string $method
     * @return bool
     */
    private function validateHttpMethod($method)
    {
        return in_array($method, ['POST', 'PUT', 'GET', 'DELETE']);
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
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @param string $appKey
     * @return HttpClient $this
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     * @return HttpClient $this
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;

        return $this;
    }
}
