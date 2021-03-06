<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\ChangeRole;

use App\Model\Auth\Entity\User\Id;
use App\Model\Auth\Entity\User\Role;
use App\Model\Auth\Entity\User\UserRepository;
use App\Model\Auth\Flusher;

class Handler
{
    /** @var UserRepository */
    private $users;

    /** @var Flusher */
    private $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users   = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->getById(new Id($command->id));
        $user->changeRole(new Role($command->role));

        $this->flusher->flush();
    }
}
