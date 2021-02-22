<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepositoryInterface;
use App\Model\User\Service\ConfirmTokenGeneratorInterface;
use App\Model\User\Service\PasswordHasherInterface;
use DateTimeImmutable;
use DomainException;

class Handler
{
    /** @var UserRepositoryInterface */
    private $users;

    /** @var PasswordHasher */
    private $hasher;

    /** @var Flusher */
    private $flusher;

    /** @var ConfirmTokenGeneratorInterface */
    private $tokenGenerator;

    /** @var ConfirmTokenSenderInterface */
    private $tokenSender;

    public function __construct(
        UserRepositoryInterface $users,
        PasswordHasherInterface $hasher,
        Flusher $flusher,
        ConfirmTokenGeneratorInterface $tokenGenerator,
        ConfirmTokerSenderInterface $tokenSender
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

        if ($this->users->hasByEmail($email)) {
            throw new DomainException('User already exists.');
        }

        $user = new User(
            Id::next(),
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenGenerator->generate(),
            new DateTimeImmutable()
        );

        $this->users->add($user);
        $this->flusher->flush();

        $this->tokenSender->send($email, $token);
    }
}
