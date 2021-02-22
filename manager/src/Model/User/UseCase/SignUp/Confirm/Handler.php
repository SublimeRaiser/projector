<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Confirm;

use App\Model\User\Entity\User\UserRepositoryInterface;
use DomainException;

class Handler
{
    /** @var UserRepositoryInterface */
    private $users;

    /** @var Flusher */
    private $flusher;

    public function __construct(UserRepositoryInterface $users, Flusher $flusher)
    {
        $this->users   = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->findByConfirmToken($command->token);
        if (!$user) {
            throw new DomainException('Invalid or already used token.');
        }
        $user->confirmSignUp();
        $this->flusher->flush();
    }
}
