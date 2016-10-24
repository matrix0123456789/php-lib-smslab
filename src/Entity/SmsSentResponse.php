<?php

namespace Ittools\Smslabs\Entity;

class SmsSentResponse
{
    private $account;
    private $smsId;

    /**
     * SmsSentResponse constructor.
     * @param int $account
     * @param string $smsId
     */
    public function __construct($account, $smsId)
    {
        $this->account = $account;
        $this->smsId   = $smsId;
    }

    /**
     * @return int
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return string
     */
    public function getSmsId()
    {
        return $this->smsId;
    }
}
