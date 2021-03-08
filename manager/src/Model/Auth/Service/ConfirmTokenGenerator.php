<?php

declare(strict_types=1);

namespace App\Model\Auth\Service;

use Ramsey\Uuid\Uuid;

class ConfirmTokenGenerator
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
