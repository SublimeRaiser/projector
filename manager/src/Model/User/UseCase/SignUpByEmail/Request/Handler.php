<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUpByEmail\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepositoryInterface;
use App\Model\User\FlusherInterface;
use App\Model\User\Service\ConfirmTokenGeneratorInterface;
use App\Model\User\Service\ConfirmTokenSenderInterface;
use App\Model\User\Service\PasswordHasherInterface;
use DateTimeImmutable;
use DomainException;

class Handler
{
    /** @var UserRepositoryInterface */
    private $userRepo;

    /** @var PasswordHasherInterface */
    private $hasher;

    /** @var FlusherInterface */
    private $flusher;

    /** @var ConfirmTokenGeneratorInterface */
    private $tokenGenerator;

    /** @var ConfirmTokenSenderInterface */
    private $tokenSender;

    /**
     * Handler constructor.
     *
     * @param UserRepositoryInterface        $userRepo
     * @param PasswordHasherInterface        $hasher
     * @param FlusherInterface               $flusher
     * @param ConfirmTokenGeneratorInterface $tokenGenerator
     * @param ConfirmTokenSenderInterface    $tokenSender
     */
    public function __construct(
        UserRepositoryInterface $userRepo,
        PasswordHasherInterface $hasher,
        FlusherInterface $flusher,
        ConfirmTokenGeneratorInterface $tokenGenerator,
        ConfirmTokenSenderInterface $tokenSender
    ) {
        $this->userRepo       = $userRepo;
        $this->hasher         = $hasher;
        $this->flusher        = $flusher;
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenSender    = $tokenSender;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->userRepo->hasByEmail($email)) {
            throw new DomainException('User already exists.');
        }

        $user = User::signUpByEmail(
            Id::next(),
            new DateTimeImmutable(),
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenGenerator->generate()
        );

        $this->userRepo->add($user);
        $this->flusher->flush();

        $this->tokenSender->send($user->getEmail(), $user->getConfirmToken());
    }
}
