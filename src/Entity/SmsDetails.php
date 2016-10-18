<?php

namespace Ittools\Smslabs\Entity;


class SmsDetails
{
    private $id;
    private $from;
    private $numberTo;
    private $status;
    private $smsCount;
    private $smsContent;
    private $price;

    /**
     * SmsDetails constructor.
     * @param $id
     * @param $from
     * @param $numberTo
     * @param $status
     * @param $smsCount
     * @param $smsContent
     * @param $price
     */
    public function __construct($id, $from, $numberTo, $status, $smsCount, $smsContent, $price)
    {
        $this->id = $id;
        $this->from = $from;
        $this->numberTo = $numberTo;
        $this->status = $status;
        $this->smsCount = $smsCount;
        $this->smsContent = $smsContent;
        $this->price = $price / 100;
    }

    /**
     * Creates SmsDetails object by array
     * @param \stdClass $sms
     * @return \Ittools\Smslabs\SmsDetails
     */
    public static function createFromResponseObject(\stdClass $sms)
    {
        return new self(
            $sms->id,
            $sms->from,
            $sms->number_to,
            $sms->status,
            $sms->sms_count,
            $sms->sms_content,
            $sms->price
        );
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return mixed
     */
    public function getNumberTo()
    {
        return $this->numberTo;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getSmsCount()
    {
        return $this->smsCount;
    }

    /**
     * @return mixed
     */
    public function getSmsContent()
    {
        return $this->smsContent;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

}