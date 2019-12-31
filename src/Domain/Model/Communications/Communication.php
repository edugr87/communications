<?php

namespace App\Domain\Model\Communications;


use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;

class Communication
{
    /**
     * @Type("string")
     */
    private $contactNumber;
    /**
     * @Type("ArrayCollection<App\Domain\Model\Communications\Sms>")
     */
    private $smss;
    /**
     * @Type("ArrayCollection<App\Domain\Model\Communications\Call>")
     */
    private $calls;

    public function __construct(string $contactNumber)
    {
        $this->contactNumber = $contactNumber;
        $this->smss = new ArrayCollection();
        $this->calls = new ArrayCollection();
    }

    public function contactNumber(): string
    {
        return $this->contactNumber;
    }

    public function smss(): ArrayCollection
    {
        return $this->smss;
    }

    public function calls(): ArrayCollection
    {
        return $this->calls;
    }

    public function addSms(Sms $component)
    {
        $this->smss->add($component);
    }

    public function addCalls(Call $component)
    {
        $this->calls->add($component);
    }
}