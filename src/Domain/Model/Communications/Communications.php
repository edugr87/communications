<?php

namespace App\Domain\Model\Communications;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;

class Communications
{
    /**
     * @Type("string")
     */
    private $telephoneNumber;
    /**
     * @Type("ArrayCollection<App\Domain\Model\Communications\Communication>")
     */
    private $communication;

    public function __construct(string $telephoneNumber)
    {
        $this->telephoneNumber = $telephoneNumber;
        $this->communication = new ArrayCollection();
    }


    public function telephoneNumber()
    {
        return $this->telephoneNumber;
    }

    public function communication()
    {
        return $this->communication;
    }

    public function addCommunication(Communication $communication)
    {
        $this->communication->set($communication->contactNumber(),$communication);
    }

    public function hasCommunication(string $communication)
    {
        return $this->communication->exists(function($value) use ($communication){
            return (string)$value === $communication;
        });
    }

    public function getCommunication(string $communication)
    {
        return $this->communication->get($communication);
    }
}