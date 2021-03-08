<?php

declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Model\Auth\Entity\User\Email;
use App\Model\Auth\Entity\User\Id;
use App\Model\Auth\Entity\User\User;
use DateTimeImmutable;

class TestUserBuilder
{
    /** @var User */
    private $user;

    public function signUpByEmail(Email $email = null, string $hash = null, string $token = null): self
    {
        $this->user = User::signUpByEmail(
            Id::next(),
            new DateTimeImmutable(),
            $email ?? new Email('test@app.test'),
            $hash  ?? 'hash',
            $token ?? 'token'
        );

        return $this;
    }

    public function signUpByNetwork(string $networkName = null, string $identity = null): self
    {
        $this->user = User::signUpByNetwork(
            Id::next(),
            new DateTimeImmutable(),
            $networkName ?? 'vk',
            $identity    ?? '0000001'
        );

        return $this;
    }

    public function confirmSignUpByEmail(): self
    {
        $this->user->confirmSignUpByEmail();

        return $this;
    }

    public function build(): User
    {
        return $this->user;
    }
}
