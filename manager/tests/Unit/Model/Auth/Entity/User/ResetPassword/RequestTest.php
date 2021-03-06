<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Auth\Entity\User\ResetPassword;

use App\Model\Auth\Entity\User\ResetToken;
use App\Tests\Builder\User\TestUserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken($tokenValue = 'token', $now->modify('+1 day'));
        $user  = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

        $user->requestPasswordReset($token, $now);

        Assert::assertEquals($tokenValue, $user->getResetToken()->getValue());
    }

    public function testAlreadyRequested(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user  = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

        $user->requestPasswordReset($token, $now);
        $this->expectExceptionMessage('Password reset is already requested.');
        $user->requestPasswordReset($token, $now);
    }

    public function testUpdateExpired(): void
    {
        $now    = new DateTimeImmutable();
        $token1 = new ResetToken('token', $now->modify('+1 day'));
        $user   = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

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
        $user  = (new TestUserBuilder())->signUpByNetwork()->build();

        $this->expectExceptionMessage('Email is not specified.');
        $user->requestPasswordReset($token, $now);
    }

    public function testWithoutConfirmedRegistration(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user  = (new TestUserBuilder())->signUpByEmail()->build();

        $this->expectExceptionMessage('User has not confirmed registration yet.');
        $user->requestPasswordReset($token, $now);
    }
}
