<?php

namespace App\Domain\Model\Communications;

interface CommunicationsRepository
{
    public function all(): array;

    public function byNumber(string $name);

}
