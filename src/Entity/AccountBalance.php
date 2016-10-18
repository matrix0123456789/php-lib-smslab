<?php

namespace Ittools\Smslabs\Entity;


class AccountBalance
{
    private $balance;

    /**
     * AccountBalance constructor.
     * @param double $balance
     */
    public function __construct($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return double
     */
    public function getBalance()
    {
        return $this->balance;
    }
}