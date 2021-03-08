<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUpByEmail\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\FlusherInterface;
use App\Model\User\Service\ConfirmTokenGenerator;
use App\Model\User\Service\ConfirmTokenSender;
use App\Model\User\Service\PasswordHasher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    /** @var UserRepository */
    private $userRepo;

    /** @var PasswordHasher */
    private $hasher;

    /** @var FlusherInterface */
    private $flusher;

    /** @var ConfirmTokenGenerator */
    private $tokenGenerator;

    /** @var ConfirmTokenSender */
    private $tokenSender;

    /**
     * Handler constructor.
     *
     * @param UserRepository        $userRepo
     * @param PasswordHasher        $hasher
     * @param FlusherInterface      $flusher
     * @param ConfirmTokenGenerator $tokenGenerator
     * @param ConfirmTokenSender    $tokenSender
     */
    public function __construct(
        UserRepository $userRepo,
        PasswordHasher $hasher,
        FlusherInterface $flusher,
        ConfirmTokenGenerator $tokenGenerator,
        ConfirmTokenSender $tokenSender
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
