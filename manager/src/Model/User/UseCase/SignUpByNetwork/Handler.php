<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUpByNetwork;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepositoryInterface;
use App\Model\User\FlusherInterface;
use DateTimeImmutable;
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
     * @param UserRepositoryInterface $userRepo
     * @param FlusherInterface        $flusher
     */
    public function __construct(UserRepositoryInterface $userRepo, FlusherInterface $flusher)
    {
        $this->userRepo = $userRepo;
        $this->flusher  = $flusher;
    }

    public function handle(Command $command): void
    {
        $networkName = $command->networkName;
        $identity    = $command->identity;
        if ($this->userRepo->hasByNetwork($networkName, $identity)) {
            throw new DomainException('User already exists.');
        }

        $user = User::signUpByNetwork(
            Id::next(),
            new DateTimeImmutable(),
            $networkName,
            $identity
        );

        $this->userRepo->add($user);
        $this->flusher->flush();
    }
}
