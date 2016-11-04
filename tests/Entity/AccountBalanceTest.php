<?php

namespace Ittoolspl\Smslabs\Tests\Entity;

use Ittoolspl\Smslabs\Entity\AccountBalance;

class AccountBalanceTest extends \PHPUnit_Framework_TestCase
{
    public function testValid()
    {
        $balance = new AccountBalance(159.36);

        $this->assertEquals(159.36, $balance->getBalance());
    }
}
