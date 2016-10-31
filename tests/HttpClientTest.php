<?php

namespace Ittoolspl\Smslabs\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Ittoolspl\Smslabs\Exception\InvalidResponseException;
use Ittoolspl\Smslabs\HttpClient;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testHttpClientAppKeySecretKeyValid()
    {
        $httpClient = new HttpClient('appKey', 'secret');
        $this->assertEquals('appKey', $httpClient->getAppKey());
        $this->assertEquals('secret', $httpClient->getSecretKey());
    }

    public function testHttpClientSetAppKeyValid()
    {
        $httpClient = new HttpClient('invalid', 'invalid');
        $httpClient->setAppKey('appKey');
        $this->assertEquals('appKey', $httpClient->getAppKey());
        $this->assertEquals('invalid', $httpClient->getSecretKey());
    }

    public function testHttpClientSetSecretKeyValid()
    {
        $httpClient = new HttpClient('invalid', 'invalid');
        $httpClient->setSecretKey('secret');
        $this->assertEquals('invalid', $httpClient->getAppKey());
        $this->assertEquals('secret', $httpClient->getSecretKey());
    }

    public function testHttpClientValid()
    {
        $guzzleResponse = new Response(200, [], \GuzzleHttp\json_encode(['data'=>'validData']));

        $httpClient = new HttpClient('appKey', 'secret');
        $httpClient->setClient($this->mockGuzzle($guzzleResponse));
        $request = $httpClient->sendRequest('/validUrl', null, 'GET');

        $this->assertEquals('validData', $request);
    }

    public function testHttpClientValidClient()
    {
        $httpClient = new HttpClient('appKey', 'secret');
        $client = $httpClient->getClient();
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testHttpClientInvalidHttpCode()
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Invalid HTTP code');

        $guzzleResponse = new Response(404, [], \GuzzleHttp\json_encode(['data'=>'inValidData']));

        $httpClient = new HttpClient('appKey', 'secret');
        $httpClient->setClient($this->mockGuzzle($guzzleResponse));
        $httpClient->sendRequest('/validUrl', null, 'GET');
    }

    public function testHttpClientInvalidMessageData()
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage('Missing data property in response');

        $guzzleResponse = new Response(200, [], '{}');

        $httpClient = new HttpClient('appKey', 'secret');
        $httpClient->setClient($this->mockGuzzle($guzzleResponse));
        $httpClient->sendRequest('/validUrl', null, 'GET');
    }

    public function testHttpClientInvalidHttpMethod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid method');

        $httpClient = new HttpClient('appKey', 'secret');
        $httpClient->sendRequest('/validUrl', null, 'TEST');
    }

    private function mockGuzzle($guzzleResponse)
    {
        $httpClientMock = \Mockery::mock('Client');
        $httpClientMock
            ->shouldReceive('request')
            ->andReturn($guzzleResponse);

        return $httpClientMock;
    }
}
