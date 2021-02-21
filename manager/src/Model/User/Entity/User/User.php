<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;

class User
{
    /** @var string */
    private $id;

    /** @var string */
    private $email;

    /** @var string */
    private $passwordHash;

    /** @var DateTimeImmutable */
    private $date;

    /**
     * User constructor.
     *
     * @param string            $id
     * @param string            $email
     * @param string            $passwordHash
     * @param DateTimeImmutable $date
     */
    public function __construct(string $id, string $email, string $passwordHash, DateTimeImmutable $date)
    {
        $this->id           = $id;
        $this->email        = $email;
        $this->passwordHash = $passwordHash;
        $this->date         = $date;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
