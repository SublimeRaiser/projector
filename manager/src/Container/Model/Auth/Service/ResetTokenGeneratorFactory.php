<?php

declare(strict_types=1);

namespace App\Container\Model\Auth\Service;

use App\Model\Auth\Service\ResetTokenGenerator;
use DateInterval;

class ResetTokenGeneratorFactory
{
    public function create(string $tokenLifetime): ResetTokenGenerator
    {
        return new ResetTokenGenerator(new DateInterval($tokenLifetime));
    }
}
