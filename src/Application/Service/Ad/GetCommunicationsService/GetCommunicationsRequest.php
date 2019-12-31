<?php

namespace App\Application\Service\Ad\GetCommunicationsService;

class GetCommunicationsRequest
{
    private $number;


    public function __construct(string $number)
    {
        $this->number = $number;

    }

    public function number(): string
    {
        return $this->number;
    }
}
