<?php

declare(strict_types=1);

namespace App\Model\User\Service;

interface PasswordHasherInterface
{
    public function hash(string $password): string;
}
