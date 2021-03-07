<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUpByEmail\Confirm;

use App\Model\User\Entity\User\UserRepository;
use App\Model\User\FlusherInterface;
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
