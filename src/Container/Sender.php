<?php

namespace Ittools\Smslabs\Container;


class Sender
{
    private $id;
    private $name;
    private $sender;

    /**
     * Sender constructor.
     * @param $id
     * @param $name
     * @param $sender
     */
    public function __construct($id, $name, $sender)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sender = $sender;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }
}
