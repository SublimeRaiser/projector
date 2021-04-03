<?php

declare(strict_types=1);

namespace App\Model\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Status
{
    public const NEW    = 'new';
    public const WAIT   = 'wait';
    public const ACTIVE = 'active';

    /** @var string */
    private $name;

    /**
     * Status constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::NEW,
            self::WAIT,
            self::ACTIVE,
        ]);
        $this->name = $name;
    }

    public static function new(): self
    {
        return new self(self::NEW);
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public function isNew(): bool
    {
        return $this->getName() === self::NEW;
    }

    public function isWait(): bool
    {
        return $this->getName() === self::WAIT;
    }

    public function isActive(): bool
    {
        return $this->getName() === self::ACTIVE;
    }

    public function isEqual(self $status): bool
    {
        return $this->getName() === $status->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
