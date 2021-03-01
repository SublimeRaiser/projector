<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\ResetToken;
use DateInterval;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class ResetTokenGenerator implements ResetTokenGeneratorInterface
{
    /** @var DateInterval */
    private $tokenLifetime;

    /**
     * ResetTokenGenerator constructor.
     *
     * @param DateInterval $tokenLifetime
     */
    public function __construct(DateInterval $tokenLifetime)
    {
        $this->tokenLifetime = $tokenLifetime;
    }

    public function generate(): ResetToken
    {
        return new ResetToken(
            Uuid::uuid4()->toString(),
            (new DateTimeImmutable())->add($this->tokenLifetime)
        );
    }
}
