<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

interface UserRepositoryInterface
{
    public function findByConfirmToken(string $token): ?User;

    public function findByResetToken(string $token): ?User;

    public function getByEmail(Email $email): User;

    public function hasByEmail(Email $email): bool;

    public function hasByNetwork(string $networkName, string $identity): bool;

    public function add(User $user): void;
}
