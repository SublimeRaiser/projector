<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\SignUpByEmail\Confirm;

use App\Model\Auth\Entity\User\UserRepository;
use App\Model\Auth\Flusher;
use DomainException;

class Handler
{
    /** @var UserRepository */
    private $users;

    /** @var Flusher */
    private $flusher;

    /**
     * Handler constructor.
     *
     * @param UserRepository $users
     * @param Flusher        $flusher
     */
    public function __construct(UserRepository $users, Flusher $flusher)
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
        $user->confirmSignUpByEmail();
        $this->flusher->flush();
    }
}
