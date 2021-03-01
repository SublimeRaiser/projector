<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\ResetPassword\Reset;

use App\Model\User\Entity\User\UserRepositoryInterface;
use App\Model\User\FlusherInterface;
use App\Model\User\Service\PasswordHasherInterface;
use DateTimeImmutable;
use DomainException;

class Handler
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @var PasswordHasherInterface
     */
    private $hasher;

    /**
     * @var FlusherInterface
     */
    private $flusher;

    /**
     * Handler constructor.
     *
     * @param UserRepositoryInterface $userRepo
     * @param PasswordHasherInterface $hasher
     * @param FlusherInterface        $flusher
     */
    public function __construct(
        UserRepositoryInterface $userRepo,
        PasswordHasherInterface $hasher,
        FlusherInterface $flusher
    ) {
        $this->userRepo = $userRepo;
        $this->hasher   = $hasher;
        $this->flusher  = $flusher;
    }

    public function handle(Command $command): void
    {
        $resetToken = $command->token;
        $password   = $command->password;

        $user = $this->userRepo->findByResetToken($resetToken);
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
