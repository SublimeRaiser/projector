<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Webmozart\Assert\Assert;

class ResetToken
{
    /** @var string */
    private $value;

    /** @var DateTimeImmutable */
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
}
