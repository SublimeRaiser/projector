<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\ResetPassword\Reset;

use App\Model\Auth\Entity\User\UserRepository;
use App\Model\Auth\Flusher;
use App\Model\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @var PasswordHasher
     */
    private $hasher;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * Handler constructor.
     *
     * @param UserRepository $users
     * @param PasswordHasher $hasher
     * @param Flusher        $flusher
     */
    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        Flusher $flusher
    ) {
        $this->users   = $users;
        $this->hasher  = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $resetToken = $command->token;
        $password   = $command->password;

        $user = $this->users->findByResetToken($resetToken);
        if (!$user) {
            throw new DomainException('Invalid or already used token.');
        }

        $user->resetPassword(
            $this->hasher->hash($password),
            new DateTimeImmutable()
        );

        $this->flusher->flush();
    }
}
