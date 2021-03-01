<?php

declare(strict_types=1);

namespace App\Tests\Factory\User;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;

class UserFactory
{
    public static function buildSignedUpByEmailUser(Email $email = null, string $hash = null, string $token = null): User {
        $user = self::buildUser();
        $user->requestSignUpByEmail(
            $email ?? new Email('test@app.test'),
            $hash  ?? 'hash',
            $token ?? 'token'
        );

        return $user;
    }

    public static function buildSignedUpByNetworkUser(string $networkName = null, string $identity = null): User
    {
        $user = self::buildUser();
        $user->signUpByNetwork(
            $networkName ?? 'vk',
            $identity    ?? '0000001'
        );

        return $user;
    }

    private static function buildUser(): User
    {
        return new User(Id::next(), new DateTimeImmutable());
    }
}
