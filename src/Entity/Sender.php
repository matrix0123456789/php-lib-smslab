<?php

namespace Ittoolspl\Smslabs\Entity;

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

    /**
     * @param array $response
     * @return Sender
     */
    public static function createFromResponseArray(array $response)
    {
        return new self(
            $response['id'],
            $response['name'],
            $response['sender']
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
