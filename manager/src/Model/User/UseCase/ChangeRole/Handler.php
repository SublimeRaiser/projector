<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\ChangeRole;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\UserRepositoryInterface;
use App\Model\User\FlusherInterface;

class Handler
{
    /** @var UserRepositoryInterface */
    private $userRepo;

    /** @var FlusherInterface */
    private $flusher;

    public function __construct(UserRepositoryInterface $userRepo, FlusherInterface $flusher)
    {
        $this->userRepo = $userRepo;
        $this->flusher  = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->userRepo->getById(new Id($command->id));
        $user->changeRole(new Role($command->role));

        $this->flusher->flush();
    }
}