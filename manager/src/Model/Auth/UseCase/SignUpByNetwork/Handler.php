<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\SignUpByNetwork;

use App\Model\Auth\Entity\User\Id;
use App\Model\Auth\Entity\User\User;
use App\Model\Auth\Entity\User\UserRepository;
use App\Model\Auth\FlusherInterface;
use DateTimeImmutable;
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
     * @param UserRepository $userRepo
     * @param FlusherInterface        $flusher
     */
    public function __construct(UserRepository $userRepo, FlusherInterface $flusher)
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