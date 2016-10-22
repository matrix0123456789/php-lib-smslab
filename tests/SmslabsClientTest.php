<?php

namespace Ittools\Smslabs\Tests;


use GuzzleHttp\Exception\ClientException;
use Ittools\Smslabs\SmslabsClient;

class SmslabsClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    function testSmslabsClientInvalidCredentials()
    {
        $this->expectException(ClientException::class);
        $this->client = new SmslabsClient('', '');
        $this->client->getAccountBalance();
    }
}
