<?php

namespace Ittoolspl\Smslabs\Tests;

use GuzzleHttp\Exception\ClientException;
use Ittoolspl\Smslabs\HttpClient;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    public function testHttpClientInvalidCredentials()
    {
        $this->expectException(ClientException::class);
        $this->client = new HttpClient('invalidKey', 'invalidSecret');
        $this->client->sendRequest('/');
    }
}
