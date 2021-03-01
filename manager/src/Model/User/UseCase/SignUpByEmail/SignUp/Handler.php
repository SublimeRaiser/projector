<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUpByEmail\SignUp;

use App\Model\User\Entity\User\UserRepositoryInterface;
use App\Model\User\FlusherInterface;
use DomainException;

class Handler
{
    /** @var UserRepositoryInterface */
    private $userRepo;

    /** @var FlusherInterface */
    private $flusher;

    /**
     * Handler constructor.
     *
     * @param UserRepositoryInterface $users
     * @param FlusherInterface        $flusher
     */
    public function __construct(UserRepositoryInterface $users, FlusherInterface $flusher)
    {
        $this->userRepo = $users;
        $this->flusher  = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->userRepo->findByConfirmToken($command->token);
        if (!$user) {
            throw new DomainException('Invalid or already used token.');
        }
        $user->signUpByEmail();
        $this->flusher->flush();
    }
}
