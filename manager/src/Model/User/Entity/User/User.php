<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;

class User
{
    private const STATUS_NEW    = 'new';
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

    /** @var Network[]|ArrayCollection */
    private $networks;

    /** @var ResetToken */
    private $resetToken;

    /**
     * User constructor.
     *
     * @param Id $id
     * @param DateTimeImmutable $date
     */
    public function __construct(Id $id, DateTimeImmutable $date)
    {
        $this->id       = $id;
        $this->date     = $date;
        $this->status   = self::STATUS_NEW;
        $this->networks = new ArrayCollection();
    }

    public function requestSignUpByEmail(Email $email, string $passwordHash, string $confirmToken): void
    {
        if (!$this->isNew()) {
            throw new DomainException('User is already signed up.');
        }
        $this->email        = $email;
        $this->passwordHash = $passwordHash;
        $this->confirmToken = $confirmToken;
        $this->status       = self::STATUS_WAIT;
    }

    public function confirmSignUpByEmail(): void
    {
        if ($this->isNew()) {
            throw new DomainException('Sign up was not requested.');
        }
        if (!$this->isWait()) {
            throw new DomainException('User has already confirmed registration.');
        }
        $this->status       = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public function signUpByNetwork(string $networkName, string $identity): void
    {
        if (!$this->isNew()) {
            throw new DomainException('User is already signed up.');
        }
        $this->networks->add(new Network($this, $networkName, $identity));
        $this->status = self::STATUS_ACTIVE;
    }

    public function attachNetwork(string $attachedNetworkName, string $identity): void
    {
        foreach ($this->networks as $userNetwork) {
            if ($userNetwork->hasName($attachedNetworkName)) {
                throw new DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $attachedNetworkName, $identity));
    }

    public function requestPasswordReset(ResetToken $resetToken, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User has not confirmed registration yet.');
        }
        if (!$this->getEmail()) {
            throw new DomainException('Email is not specified.');
        }
        if ($this->getResetToken() && !$this->getResetToken()->hasExpiredBy($date)) {
            throw new DomainException('Password reset is already requested.');
        }
        $this->resetToken = $resetToken;
    }

    public function resetPassword(string $passwordHash, DateTimeImmutable $date): void
    {
        if (!$this->getResetToken()) {
            throw new DomainException('Password reset was not requested.');
        }
        if ($this->getResetToken()->hasExpiredBy($date)) {
            throw new DomainException('Password reset token has expired.');
        }
        $this->passwordHash = $passwordHash;
        $this->resetToken   = null;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): ?Email
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

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    public function getResetToken(): ?ResetToken
    {
       return $this->resetToken;
    }
}
