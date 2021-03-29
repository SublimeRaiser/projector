<?php

declare(strict_types=1);

namespace App\Model\Auth\Service;

use RuntimeException;

class PasswordHasher
{
    public function hash(string $password): string
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new RuntimeException('Unable to hash the password.');
        }

        return $hash;
    }

    public function isValid(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
