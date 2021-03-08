<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\SignUpByEmail\Confirm;

use App\Model\Auth\Entity\User\UserRepository;
use App\Model\Auth\FlusherInterface;
use DomainException;

class Handler
{
    /** @var UserRepository */
    private $userRepo;

    /** @var FlusherInterface */
    private $flusher;

    /**
     * Handler constructor.
     *
     * @param UserRepository $users
     * @param FlusherInterface        $flusher
     */
    public function __construct(UserRepository $users, FlusherInterface $flusher)
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
        $user->confirmSignUpByEmail();
        $this->flusher->flush();
    }
}
