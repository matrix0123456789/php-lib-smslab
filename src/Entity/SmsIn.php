<?php

namespace Ittoolspl\Smslabs\Entity;

class SmsIn
{
    private $id;
    private $count;
    private $content;
    private $numberTo;
    private $receiveTime;
    private $numberFrom;
    private $status;

    /**
     * SmsIn constructor.
     * @param string $id
     * @param int $count
     * @param string $content
     * @param string $numberTo
     * @param int $receiveTime
     * @param string $numberFrom
     * @param int $status
     */
    public function __construct($id, $count, $content, $numberTo, $receiveTime, $numberFrom, $status)
    {
        $receiveTimeDT = new \DateTime();
        $receiveTimeDT->setTimestamp($receiveTime);

        $this->id          = $id;
        $this->count       = $count;
        $this->content     = $content;
        $this->numberTo    = $numberTo;
        $this->receiveTime = $receiveTimeDT;
        $this->numberFrom  = $numberFrom;
        $this->status      = $status;
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
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
    public function getReceiveTime()
    {
        return $this->receiveTime;
    }

    /**
     * @return string
     */
    public function getNumberFrom()
    {
        return $this->numberFrom;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
}
