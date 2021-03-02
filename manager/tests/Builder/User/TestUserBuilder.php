<?php

declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use Exception;

class TestUserBuilder
{
    private $user;

    public function __construct()
    {
        $this->user = new User(Id::next(), new DateTimeImmutable());
    }

    public function requestSignUpByEmail(Email $email = null, string $hash = null, string $token = null): self
    {
        $this->user->requestSignUpByEmail(
            $email ?? new Email('test@app.test'),
            $hash  ?? 'hash',
            $token ?? 'token'
        );

        return $this;
    }

    public function requestSignUpByNetwork(string $networkName = null, string $identity = null): self
    {
        $this->user->signUpByNetwork(
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
