<?php

namespace Ittools\Smslabs;


class AccountBalance
{
    private $balance;

    /**
     * AccountBalance constructor.
     * @param $balance
     */
    public function __construct($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }
}