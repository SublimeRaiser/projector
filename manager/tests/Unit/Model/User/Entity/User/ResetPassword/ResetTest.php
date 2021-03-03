<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\ResetPassword;

use App\Model\User\Entity\User\ResetToken;
use App\Tests\Builder\User\TestUserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user  = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

        $user->requestPasswordReset($token, $now);
        $user->resetPassword($hash = 'hash_new', $now);

        Assert::assertEquals($hash, $user->getPasswordHash());
        Assert::assertNull($user->getResetToken());
    }

    public function testExpiredToken(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user  = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

        $user->requestPasswordReset($token, $now);
        $this->expectExceptionMessage('Password reset token has expired.');
        $user->resetPassword('hash_new', $now->modify('+2 day'));
    }

    public function testNotRequested(): void
    {
        $now  = new DateTimeImmutable();
        $user = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

        $this->expectExceptionMessage('Password reset was not requested.');
        $user->resetPassword('hash_new', $now);
    }
}
