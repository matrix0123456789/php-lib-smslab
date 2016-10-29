<?php

namespace Ittoolspl\Smslabs\Tests;

use GuzzleHttp\Exception\ClientException;
use Ittoolspl\Smslabs\SmslabsClient;

class SmslabsClientTest extends \PHPUnit_Framework_TestCase
{
    private $validResult = [
        0 => [
            'phone_number' => '+48790222500',
            'message' => 'Top secret SMS',
            'flash' => 0,
            'expiration' => 0,
            'sender_id' => 'ITTools',
        ],
    ];

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testSmslabsClientInvalidCredentials()
    {
        $this->expectException(ClientException::class);
        $client = new SmslabsClient('', '');
        $client->getAccountBalance();
    }

    public function testSmslabsClientAddSmsToQueueValid()
    {
        $sms = new  SmslabsClient('', '');
        $sms->setSenderId('ITTools');
        $sms->add('+48790222500', 'Top secret SMS');
        $queue = $sms->getSmsQueue();

        $this->assertTrue($this->validResult == $queue);
        $this->assertCount(1, $queue);
    }

    public function testSmslabsClientAddSmsToQueueValidThreeMsg()
    {
        $validResultThree = [
            0 => [
                'phone_number' => '+48790222500',
                'message' => 'Top secret SMS',
                'flash' => 0,
                'expiration' => 0,
                'sender_id' => 'ITTools',
            ],
            1 => [
                'phone_number' => '+48790222501',
                'message' => 'Top secret SMM',
                'flash' => 0,
                'expiration' => 0,
                'sender_id' => 'ITTools',
            ],
            2 => [
                'phone_number' => '+48790222502',
                'message' => 'Top secret SMV',
                'flash' => 0,
                'expiration' => 0,
                'sender_id' => 'ITTools',
            ],
        ];

        $sms = new SmslabsClient('', '');
        $sms->setSenderId('ITTools');
        $sms->add('+48790222500', 'Top secret SMS');
        $sms->add('+48790222501', 'Top secret SMM');
        $sms->add('+48790222502', 'Top secret SMV');
        $queue = $sms->getSmsQueue();

        $this->assertTrue($validResultThree == $queue);
        $this->assertCount(3, $queue);
    }

    public function testSmslabsClientAddSmsToQueueInvalidMessage()
    {
        $sms = new  SmslabsClient('', '');
        $sms->setSenderId('ITTools');
        $sms->add('+48790222500', 'Not secret SMS');

        $this->assertNotTrue($this->validResult == $sms->getSmsQueue());
    }

    public function testSmslabsClientAddSmsToQueueInvalidNumber()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid phone number');

        $sms = new SmslabsClient('', '');
        $sms->setSenderId('ITTools');
        $sms->add('+48222500', 'Not secret SMS');
    }

    public function testSmslabsClientSendValid()
    {
        $account = [
            'account' => 1593,
            'sms_id' => '5813f6f4b5ca20c1767b23ca',
        ];

        $httpClientMock = \Mockery::mock('HttpClient');
        $httpClientMock
            ->shouldReceive('sendRequest')
            ->andReturn($account);

        $sms = new SmslabsClient('', '');
        $sms->setSenderId('ITTools.pl');
        $sms->add('+48790222500', 'test1');
        $sms->setClient($httpClientMock);
        $sms->send();

        $status = $sms->getSentStatus();

        $this->assertEquals(1593, $status[0]->getAccount());
        $this->assertEquals('5813f6f4b5ca20c1767b23ca', $status[0]->getSmsId());
    }
}
