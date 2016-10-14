<?php

namespace Ittools\Smslabs\Container;


class OutSms
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
     * OutSms constructor.
     * @param $id
     * @param $deliveryTime
     * @param $count
     * @param $price
     * @param $content
     * @param $numberTo
     * @param $incomingTime
     * @param $status
     * @param $statusD
     * @param $numberFrom
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
        $deliveryTimeDT = new \DateTime();
        $deliveryTimeDT->setTimestamp($deliveryTime);

        $incomingTimeDT = new \DateTime();
        $incomingTimeDT->setTimestamp($incomingTime);

        $this->id = $id;
        $this->deliveryTime = $deliveryTimeDT;
        $this->count = $count;
        $this->price = $price;
        $this->content = $content;
        $this->numberTo = $numberTo;
        $this->incomingTime = $incomingTimeDT;
        $this->status = $status;
        $this->statusD = $statusD;
        $this->numberFrom = $numberFrom;
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
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
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
    public function getIncomingTime()
    {
        return $this->incomingTime;
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
    public function getStatusD()
    {
        return $this->statusD;
    }

    /**
     * @return mixed
     */
    public function getNumberFrom()
    {
        return $this->numberFrom;
    }
}