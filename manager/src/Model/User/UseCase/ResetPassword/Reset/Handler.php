<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\ResetPassword\Reset;

use App\Model\User\Entity\User\UserRepository;
use App\Model\User\FlusherInterface;
use App\Model\User\Service\PasswordHasher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var PasswordHasher
     */
    private $hasher;

    /**
     * @var FlusherInterface
     */
    private $flusher;

    /**
     * Handler constructor.
     *
     * @param UserRepository   $userRepo
     * @param PasswordHasher   $hasher
     * @param FlusherInterface $flusher
     */
    public function __construct(
        UserRepository $userRepo,
        PasswordHasher $hasher,
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
