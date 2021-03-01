<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\ResetPassword;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken($tokenValue = 'token', $now->modify('+1 day'));
        $user  = $this->buildSignedUpByEmailUser();

        $user->requestPasswordReset($token, $now);

        Assert::assertEquals($tokenValue, $user->getResetToken()->getValue());
    }

    public function testAlreadyRequested(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user  = $this->buildSignedUpByEmailUser();

        $user->requestPasswordReset($token, $now);
        $this->expectExceptionMessage('Password reset is already requested.');
        $user->requestPasswordReset($token, $now);
    }

    public function testUpdateExpired(): void
    {
        $now    = new DateTimeImmutable();
        $token1 = new ResetToken('token', $now->modify('+1 day'));
        $user   = $this->buildSignedUpByEmailUser();

        $user->requestPasswordReset($token1, $now);
        Assert::assertEquals($token1, $user->getResetToken());

        $token2 = new ResetToken('token', $now->modify('+3 day'));
        $user->requestPasswordReset($token2, $now->modify('+2 day'));
        Assert::assertEquals($token2, $user->getResetToken());
    }

    public function testWithoutEmail(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user  = $this->buildSignedUpByNetworkUser();

        $this->expectExceptionMessage('Email is not specified.');
        $user->requestPasswordReset($token, $now);
    }

    private function buildSignedUpByEmailUser(): User
    {
        $user = new User(Id::next(), new DateTimeImmutable());
        $user->requestSignUpByEmail(new Email('test@app.test'), 'hash', 'token');

        return $user;
    }

    private function buildSignedUpByNetworkUser(): User
    {
        $user = new User(Id::next(), new DateTimeImmutable());
        $user->signUpByNetwork('vk', '0000001');

        return $user;
    }
}
