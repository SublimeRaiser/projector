<?php

namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_network", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"network_name", "identity"})
 * })
 */
class Network
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="networks ")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $networkName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $identity;

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
