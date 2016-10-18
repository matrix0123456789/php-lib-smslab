<?php

namespace Ittools\Smslabs\Entity;


class InSms
{
    private $id;
    private $count;
    private $content;
    private $numberTo;
    private $receiveTime;
    private $numberFrom;
    private $status;

    /**
     * InSms constructor.
     * @param $id
     * @param $count
     * @param $content
     * @param $numberTo
     * @param $receiveTime
     * @param $numberFrom
     * @param $status
     */
    public function __construct($id, $count, $content, $numberTo, $receiveTime, $numberFrom, $status)
    {
        $receiveTimeDT = new \DateTime();
        $receiveTimeDT->setTimestamp($receiveTime);

        $this->id = $id;
        $this->count = $count;
        $this->content = $content;
        $this->numberTo = $numberTo;
        $this->receiveTime = $receiveTimeDT;
        $this->numberFrom = $numberFrom;
        $this->status = $status;
    }

    public static function createFromResponseObject(\stdClass $respObj)
    {
        return new self(
            $respObj->_id,
            $respObj->s_cnt,
            $respObj->s_con,
            $respObj->no_to,
            $respObj->in_t,
            $respObj->no_fr,
            $respObj->stat
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
    public function getCount()
    {
        return $this->count;
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
    public function getReceiveTime()
    {
        return $this->receiveTime;
    }

    /**
     * @return mixed
     */
    public function getNumberFrom()
    {
        return $this->numberFrom;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }
}