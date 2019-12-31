<?php

namespace App\Domain\Model\Communications;


use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;


class Call
{
    /**
     * @Type("boolean")
     */
    private $incoming;
    /**
     * @Type("string")
     */
    private $name;
    /**
     * @Type("string")
     */
    private $date;
    /**
     * @Type("string")
     */
    private $duration;

    public function __construct(bool $incoming, $name, $date, $duration)
    {
        $this->incoming = $incoming;
        $this->name = $name;
        $this->date = $date;
        $this->duration = $duration;
    }

    public function incoming(): bool
    {
        return $this->incoming;
    }

    public function name()
    {
        return $this->name;
    }

    public function dateTime()
    {
        return $this->date;
    }

    public function duration()
    {
        return $this->duration;
    }
}