<?php

namespace Ittoolspl\Smslabs\Tests\Entity;

use Ittoolspl\Smslabs\Entity\SmsSentResponse;

class SmsSentResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testValid()
    {
        $balance = new SmsSentResponse(12345, 'abcdf');

        $this->assertEquals('12345', $balance->getAccount());
        $this->assertEquals('abcdf', $balance->getSmsId());
    }
}
