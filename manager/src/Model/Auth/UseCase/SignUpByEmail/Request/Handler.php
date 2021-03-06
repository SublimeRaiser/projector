<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\SignUpByEmail\Request;

use App\Model\Auth\Entity\User\Email;
use App\Model\Auth\Entity\User\Id;
use App\Model\Auth\Entity\User\User;
use App\Model\Auth\Entity\User\UserRepository;
use App\Model\Auth\Flusher;
use App\Model\Auth\Service\ConfirmTokenGenerator;
use App\Model\Auth\Service\ConfirmTokenSender;
use App\Model\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    /** @var UserRepository */
    private $users;

    /** @var PasswordHasher */
    private $hasher;

    /** @var Flusher */
    private $flusher;

    /** @var ConfirmTokenGenerator */
    private $tokenGenerator;

    /** @var ConfirmTokenSender */
    private $tokenSender;

    /**
     * Handler constructor.
     *
     * @param UserRepository        $users
     * @param PasswordHasher        $hasher
     * @param Flusher               $flusher
     * @param ConfirmTokenGenerator $tokenGenerator
     * @param ConfirmTokenSender    $tokenSender
     */
    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        Flusher $flusher,
        ConfirmTokenGenerator $tokenGenerator,
        ConfirmTokenSender $tokenSender
    ) {
        $this->users          = $users;
        $this->hasher         = $hasher;
        $this->flusher        = $flusher;
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenSender    = $tokenSender;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->existsByEmail($email)) {
            throw new DomainException('User already exists.');
        }

        $user = User::signUpByEmail(
            Id::next(),
            new DateTimeImmutable(),
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenGenerator->generate()
        );

        $this->users->add($user);
        $this->flusher->flush();

        $this->tokenSender->send($user->getEmail(), $user->getConfirmToken());
    }
}
