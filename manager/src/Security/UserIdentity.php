<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\Auth\Entity\User\Status;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $role;

    /**
     * @var string
     */
    private $status;

    /**
     * UserIdentity constructor.
     *
     * @param string $id
     * @param string $username
     * @param string $password
     * @param string $role
     * @param string $status
     */
    public function __construct(string $id, string $username, string $password, string $role, string $status)
    {
        $this->id       = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role     = $role;
        $this->status   = $status;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * Checks if the user is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === Status::ACTIVE;
    }
}
