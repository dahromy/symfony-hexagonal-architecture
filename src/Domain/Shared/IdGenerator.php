<?php

namespace App\Domain\Shared;

use Symfony\Component\Uid\Uuid;

class IdGenerator
{
    public function generate(): string
    {
        return Uuid::v6()->toRfc4122();
    }
}
