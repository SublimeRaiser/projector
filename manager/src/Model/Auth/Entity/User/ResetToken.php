<?php

declare(strict_types=1);

namespace App\Model\Auth\Entity\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class ResetToken
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $value;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $expiresAt;

    /**
     * ResetToken constructor.
     *
     * @param string            $token
     * @param DateTimeImmutable $expiresAt
     */
    public function __construct(string $token, DateTimeImmutable $expiresAt)
    {
        Assert::notEmpty($token);
        $this->value     = $token;
        $this->expiresAt = $expiresAt;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function hasExpiredBy(DateTimeImmutable $date): bool
    {
        return $date >= $this->expiresAt;
    }

    /**
     * @internal for postLoad callback
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->value);
    }
}
