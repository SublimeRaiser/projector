<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use DomainException;

class User
{
    private const STATUS_WAIT   = 'wait';
    private const STATUS_ACTIVE = 'active';

    /** @var string */
    private $id;

    /** @var Email */
    private $email;

    /** @var string */
    private $passwordHash;

    /** @var string */
    private $confirmToken;

    /** @var DateTimeImmutable */
    private $date;

    /** @var string */
    private $status;

    /**
     * User constructor.
     *
     * @param Id                $id
     * @param Email             $email
     * @param string            $passwordHash
     * @param string            $confirmToken
     * @param DateTimeImmutable $date
     */
    public function __construct(Id $id, Email $email, string $passwordHash, string $confirmToken, DateTimeImmutable $date)
    {
        $this->id           = $id;
        $this->email        = $email;
        $this->passwordHash = $passwordHash;
        $this->confirmToken = $confirmToken;
        $this->date         = $date;
        $this->status       = self::STATUS_WAIT;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('User has already confirmed registration.');
        }
        $this->status       = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }
}
