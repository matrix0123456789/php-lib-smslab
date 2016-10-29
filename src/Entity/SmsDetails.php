<?php

namespace Ittoolspl\Smslabs\Entity;

class SmsDetails
{
    private $id;
    private $from;
    private $numberTo;
    private $status;
    private $smsCount;
    private $smsContent;
    private $priceInGrosz;

    /**
     * SmsDetails constructor.
     * @param string $id
     * @param string $from
     * @param string $numberTo
     * @param int $status
     * @param int $smsCount
     * @param string $smsContent
     * @param double $priceInGrosz
     */
    public function __construct($id, $from, $numberTo, $status, $smsCount, $smsContent, $priceInGrosz)
    {
        $this->id           = $id;
        $this->from         = $from;
        $this->numberTo     = $numberTo;
        $this->status       = $status;
        $this->smsCount     = $smsCount;
        $this->smsContent   = $smsContent;
        $this->priceInGrosz = $priceInGrosz;
    }

    /**
     * Creates SmsDetails object by array
     * @param array $response
     * @return \Ittoolspl\Smslabs\Entity\SmsDetails
     */
    public static function createFromResponseArray(array $response)
    {
        return new self(
            $response['id'],
            $response['from'],
            $response['number_to'],
            $response['status'],
            $response['sms_count'],
            $response['sms_content'],
            ($response['price'] / 100)
        );
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getNumberTo()
    {
        return $this->numberTo;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getSmsCount()
    {
        return $this->smsCount;
    }

    /**
     * @return string
     */
    public function getSmsContent()
    {
        return $this->smsContent;
    }

    /**
     * @return double
     */
    public function getPriceInGrosz()
    {
        return $this->priceInGrosz;
    }
}
