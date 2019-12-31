<?php

namespace App\Domain\Model\Communications;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;


class Sms
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

    public function __construct(bool $incoming, $name, $date)
    {
        $this->incoming = $incoming;
        $this->name = $name;
        $this->date = $date;
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
}