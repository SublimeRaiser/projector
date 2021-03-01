<?php

namespace App\Model\User\Entity\User;

use Ramsey\Uuid\Uuid;

class Network
{
    /** @var string */
    private $id;

    /** @var string */
    private $networkName;

    /** @var string */
    private $identity;

    /** @var User */
    private $user;

    /**
     * Network constructor.
     *
     * @param User   $user
     * @param string $networkName
     * @param string $identity
     */
    public function __construct(User $user, string $networkName, string $identity)
    {
        $this->id          = Uuid::uuid4()->toString();
        $this->user        = $user;
        $this->networkName = $networkName;
        $this->identity    = $identity;
    }

    public function getNetworkName(): string
    {
        return $this->networkName;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function hasName(string $networkName): bool
    {
        return $this->networkName === $networkName;
    }
}
