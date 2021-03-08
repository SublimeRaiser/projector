<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\ResetPassword\Request;

use App\Model\Auth\Entity\User\Email;
use App\Model\Auth\Entity\User\UserRepository;
use App\Model\Auth\FlusherInterface;
use App\Model\Auth\Service\ResetTokenGenerator;
use App\Model\Auth\Service\ResetTokenSender;
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
     * @var ResetTokenSender
     */
    private $tokenSender;

    /**
     * Handler constructor.
     *
     * @param UserRepository      $userRepo
     * @param FlusherInterface    $flusher
     * @param ResetTokenGenerator $tokenGenerator
     * @param ResetTokenSender    $tokenSender
     */
    public function __construct(
        UserRepository $userRepo,
        FlusherInterface $flusher,
        ResetTokenGenerator $tokenGenerator,
        ResetTokenSender $tokenSender
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
