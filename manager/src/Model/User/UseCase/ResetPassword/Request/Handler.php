<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\ResetPassword\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\FlusherInterface;
use App\Model\User\Service\ResetTokenGenerator;
use App\Model\User\Service\ResetTokenSenderInterface;
use DateTimeImmutable;

class Handler
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var FlusherInterface
     */
    private $flusher;

    /**
     * @var ResetTokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var ResetTokenSenderInterface
     */
    private $tokenSender;

    /**
     * Handler constructor.
     *
     * @param UserRepository            $userRepo
     * @param FlusherInterface          $flusher
     * @param ResetTokenGenerator       $tokenGenerator
     * @param ResetTokenSenderInterface $tokenSender
     */
    public function __construct(
        UserRepository $userRepo,
        FlusherInterface $flusher,
        ResetTokenGenerator $tokenGenerator,
        ResetTokenSenderInterface $tokenSender
    ) {
        $this->userRepo       = $userRepo;
        $this->flusher        = $flusher;
        $this->tokenGenerator = $tokenGenerator;
        $this->tokenSender    = $tokenSender;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);
        $user  = $this->userRepo->getByEmail($email);
        $user->requestPasswordReset(
            $this->tokenGenerator->generate(),
            new DateTimeImmutable()
        );

        $this->flusher->flush();

        $this->tokenSender->send($user->getEmail(), $user->getResetToken());
    }
}
