<?php

declare(strict_types=1);

namespace App\Model\Auth\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="user_user", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 *     @ORM\UniqueConstraint(columns={"reset_token_value"}),
 *     @ORM\UniqueConstraint(columns={"confirm_token"}),
 * })
 */
class User
{
    /**
     * @var Id
     *
     * @ORM\Id
     * @ORM\Column(type="user_user_id")
     */
    private $id;

    /**
     * @var Email
     *
     * @ORM\Column(type="user_user_email", nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passwordHash;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmToken;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Network[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Network", mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $networks;

    /**
     * @var ResetToken|null
     *
     * @ORM\Embedded(class="ResetToken", columnPrefix="reset_token_")
     */
    private $resetToken;

    /**
     * @var Status
     *
     * @ORM\Column(type="user_user_status")
     */
    private $status;

    /**
     * @var Role
     *
     * @ORM\Column(type="user_user_role")
     */
    private $role;

    /**
     * User constructor.
     *
     * @param Id                $id
     * @param DateTimeImmutable $date
     */
    private function __construct(Id $id, DateTimeImmutable $date)
    {
        $this->id        = $id;
        $this->createdAt = $date;
        $this->networks  = new ArrayCollection();
        $this->status    = Status::new();
        $this->role      = Role::user();
    }

    public static function signUpByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        string $confirmToken
    ): self {
        $user               = new self($id, $date);
        $user->email        = $email;
        $user->passwordHash = $passwordHash;
        $user->confirmToken = $confirmToken;
        $user->changeStatus(Status::wait());

        return $user;
    }

    public static function signUpByNetwork(
        Id $id,
        DateTimeImmutable $date,
        string $networkName,
        string $identity
    ): self {
        $user = new self($id, $date);
        $user->networks->add(new Network($user, $networkName, $identity));
        $user->changeStatus(Status::active());

        return $user;
    }

    public function confirmSignUpByEmail(): void
    {
        if ($this->isNew()) {
            throw new DomainException('Sign up was not requested.');
        }
        if (!$this->isWait()) {
            throw new DomainException('User has already confirmed registration.');
        }
        $this->changeStatus(Status::active());
        $this->confirmToken = null;
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

    public function changeStatus(Status $newStatus): void
    {
        if ($this->getStatus()->isEqual($newStatus)) {
            throw new DomainException('This status has already been assigned.');
        }
        $this->status = $newStatus;
    }

    public function changeRole(Role $newRole): void
    {
        if ($this->getRole()->isEqual($newRole)) {
            throw new DomainException('This role has already been assigned.');
        }
        $this->role = $newRole;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isNew(): bool
    {
        return $this->status->isNew();
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    public function getResetToken(): ?ResetToken
    {
       return $this->resetToken;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->getResetToken()->isEmpty()) {
            $this->resetToken = null;
        }
    }
}
