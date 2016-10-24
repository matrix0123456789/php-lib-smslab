<?php

namespace Ittools\Smslabs\Entity;

class Sender
{
    private $id;
    private $name;
    private $sender;

    /**
     * Sender constructor.
     * @param string $id
     * @param string $name
     * @param string $sender
     */
    public function __construct($id, $name, $sender)
    {
        $this->id     = $id;
        $this->name   = $name;
        $this->sender = $sender;
    }

    public static function createFromResponseObject(\stdClass $respObj)
    {
        return new self(
            $respObj->id,
            $respObj->name,
            $respObj->sender
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }
}
