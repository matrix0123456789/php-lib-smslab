<?php
/**
 * Created by PhpStorm.
 * User: janusz
 * Date: 29.10.16
 * Time: 04:04
 */

namespace Ittoolspl\Smslabs\Tests\Entity;

use Ittoolspl\Smslabs\Entity\SmsSentResponse;

class SmsSentResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Ittoolspl\Smslabs\Entity\SmsSentResponse
     */
    public function testValid()
    {
        $balance = new SmsSentResponse(12345, 'abcdf');

        $this->assertEquals('12345', $balance->getAccount());
        $this->assertEquals('abcdf', $balance->getSmsId());
    }
}
