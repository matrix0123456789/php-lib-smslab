<?php

namespace Ittoolspl\Smslabs\Tests;

use Ittoolspl\Smslabs\Exception\EmptySMSQueueException;
use Ittoolspl\Smslabs\SmslabsClient;

class SmslabsClientTest extends \PHPUnit_Framework_TestCase
{
    private $validResult = [
        0 => [
            'phone_number' => '+48790000000',
            'message' => 'Top secret SMS',
            'flash' => 0,
            'expiration' => 0,
            'sender_id' => 'ITtools',
        ],
        1 => [
            'phone_number' => '+48790000000',
            'message' => 'message',
            'flash' => true,
            'expiration' => 10,
            'sender_id' => 'ITtools',
        ],
    ];

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testSmslabsClientAddSmsToQueueValid()
    {
        $sms = new  SmslabsClient('', '');
        $sms->setSenderId('ITtools');
        $sms->add('+48790000000', 'Top secret SMS');
        $sms->add('+48790000000', 'message', true, 10);
        $queue = $sms->getSmsQueue();

        $this->assertTrue($this->validResult == $queue);
        $this->assertCount(2, $queue);
    }

    public function testSmslabsClientAddSmsToQueueValidThreeMsg()
    {
        $validResultThree = [
            0 => [
                'phone_number' => '+48790000000',
                'message' => 'Top secret SMS',
                'flash' => 0,
                'expiration' => 0,
                'sender_id' => 'ITtools',
            ],
            1 => [
                'phone_number' => '+48790000001',
                'message' => 'Top secret SMM',
                'flash' => 0,
                'expiration' => 0,
                'sender_id' => 'ITtools',
            ],
            2 => [
                'phone_number' => '+48790000002',
                'message' => 'Top secret SMV',
                'flash' => 0,
                'expiration' => 0,
                'sender_id' => 'ITtools',
            ],
        ];

        $sms = new SmslabsClient('', '');
        $sms->setSenderId('ITtools');
        $sms->add('+48790000000', 'Top secret SMS');
        $sms->add('+48790000001', 'Top secret SMM');
        $sms->add('+48790000002', 'Top secret SMV');
        $queue = $sms->getSmsQueue();

        $this->assertTrue($validResultThree == $queue);
        $this->assertCount(3, $queue);
    }

    public function testSmslabsClientAddSmsToQueueInvalidMessage()
    {
        $sms = new  SmslabsClient('', '');
        $sms->setSenderId('ITtools');
        $sms->add('+48790000000', 'Not secret SMS');

        $this->assertNotTrue($this->validResult == $sms->getSmsQueue());
    }

    public function testSmslabsClientAddSmsToQueueInvalidNumber()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid phone number');

        $sms = new SmslabsClient('', '');
        $sms->setSenderId('ITtools');
        $sms->add('+48000000', 'Not secret SMS');
    }

    public function testSmslabsClientSendValid()
    {
        $sendDate = new \DateTime();

        $sms = new SmslabsClient('', '');
        $sms->setSenderId('ITtools.pl');
        $sms->setIsFlashMessage(true);
        $sms->setExpirationMinutes(900);
        $sms->setSendDateTime($sendDate);
        $sms->add('+48790000000', 'message');
        $sms->setClient($this->mockHttpClient());
        $sms->send();

        $status = $sms->getSentStatus();


        $this->assertInstanceOf('HttpClient', $sms->getClient());
        $this->assertEquals('ITtools.pl', $sms->getSenderId());
        $this->assertTrue($sms->isIsFlashMessage());
        $this->assertEquals(900, $sms->getExpirationMinutes());
        $this->assertEquals($sendDate, $sms->getSendDateTime());
        $this->assertEquals(1593, $status[0]->getAccount());
        $this->assertEquals('5813f6f4b5ca20c1767b23ca', $status[0]->getSmsId());
    }

    public function testSmslabsClientSendEmptyQueue()
    {
        $this->expectException(EmptySMSQueueException::class);
        $this->expectExceptionMessage('No messages to send');

        $sms = new SmslabsClient('', '');
        $sms->setSenderId('valid');
        $sms->setClient($this->mockHttpClient());
        $sms->send();
    }

    public function testSmslabsClientSendInvalidExpirationMinutes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Valid values: 1 - 5520');

        $sms = new SmslabsClient('', '');
        $sms->setExpirationMinutes(0);
    }

    public function testSmslabsClientSendMissingSenderId()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('SenderId is missing');

        $sms = new SmslabsClient('', '');
        $sms->add('+48790000000', 'message');
        $sms->send();
    }

    private function mockHttpClient()
    {
        $account = [
            'account' => 1593,
            'sms_id' => '5813f6f4b5ca20c1767b23ca',
        ];

        $httpClientMock = \Mockery::mock('HttpClient');
        $httpClientMock
            ->shouldReceive('sendRequest')
            ->andReturn($account);

        return $httpClientMock;
    }
}
