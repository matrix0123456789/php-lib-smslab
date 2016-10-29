<?php

namespace Ittoolspl\Smslabs\Entity;

class SmsOut
{
    private $id;
    private $deliveryTime;
    private $count;
    private $price;
    private $content;
    private $numberTo;
    private $incomingTime;
    private $status;
    private $statusD;
    private $numberFrom;

    /**
     * SmsOut constructor.
     * @param string $id
     * @param int $deliveryTime
     * @param int $count
     * @param double $price
     * @param string $content
     * @param string $numberTo
     * @param int $incomingTime
     * @param int $status
     * @param string $statusD
     * @param string $numberFrom
     */
    public function __construct(
        $id,
        $deliveryTime,
        $count,
        $price,
        $content,
        $numberTo,
        $incomingTime,
        $status,
        $statusD,
        $numberFrom
    ) {
        if ($deliveryTime !== null) {
            $deliveryTimeDT = new \DateTime();
            $deliveryTimeDT->setTimestamp($deliveryTime);
            $this->deliveryTime = $deliveryTimeDT;
        }

        $incomingTimeDT = new \DateTime();
        $incomingTimeDT->setTimestamp($incomingTime);
        $this->incomingTime = $incomingTimeDT;

        $this->id         = $id;
        $this->count      = $count;
        $this->price      = $price;
        $this->content    = $content;
        $this->numberTo   = $numberTo;
        $this->status     = $status;
        $this->statusD    = $statusD;
        $this->numberFrom = $numberFrom;
    }

    /**
     * @param \stdClass $respObj
     * @return SmsOut
     */
    public static function createFromResponseObject(\stdClass $respObj)
    {
        return new self(
            $respObj->_id,
            property_exists($respObj, 'del_t') ? $respObj->del_t : null,
            $respObj->s_cnt,
            $respObj->price,
            $respObj->s_con,
            $respObj->no_to,
            $respObj->in_t,
            $respObj->stat,
            $respObj->stat_d,
            $respObj->no_fr
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
     * @return \DateTime
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return double
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getNumberTo()
    {
        return $this->numberTo;
    }

    /**
     * @return \DateTime
     */
    public function getIncomingTime()
    {
        return $this->incomingTime;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusD()
    {
        return $this->statusD;
    }

    /**
     * @return string
     */
    public function getNumberFrom()
    {
        return $this->numberFrom;
    }
}
