<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use RuntimeException;

class PasswordHasher implements PasswordHasherInterface
{
    public function hash(string $password): string
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new RuntimeException('Unable to hash the password.');
        }

        return $hash;
    }
}