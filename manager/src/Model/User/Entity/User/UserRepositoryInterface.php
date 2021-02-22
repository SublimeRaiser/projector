<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

interface UserRepositoryInterface
{
    public function hasByEmail(Email $email): bool;

    public function add(User $user): void;

    public function findByConfirmToken(string $token): ?User;
}
