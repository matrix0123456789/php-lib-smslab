<?php

namespace Ittoolspl\Smslabs\Tests;

use Ittoolspl\Smslabs\HttpClient;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @covers \Ittoolspl\Smslabs\HttpClient
     */
    public function testHttpClientAppKeySecretKey()
    {
        $httpClient = new HttpClient('appKey', 'secret');
        $this->assertEquals('appKey', $httpClient->getAppKey());
        $this->assertEquals('secret', $httpClient->getSecretKey());
    }
}
