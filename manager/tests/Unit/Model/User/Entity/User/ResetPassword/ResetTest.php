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

class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user  = $this->buildSignedUpByEmailUser();

        $user->requestPasswordReset($token, $now);
        $user->resetPassword($hash = 'hash_new', $now);

        Assert::assertEquals($hash, $user->getPasswordHash());
        Assert::assertNull($user->getResetToken());
    }

    public function testExpiredToken(): void
    {
        $now   = new DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));
        $user  = $this->buildSignedUpByEmailUser();

        $user->requestPasswordReset($token, $now);
        $this->expectExceptionMessage('Password reset token has expired.');
        $user->resetPassword('hash_new', $now->modify('+2 day'));
    }

    public function testNotRequested(): void
    {
        $now  = new DateTimeImmutable();
        $user = $this->buildSignedUpByEmailUser();

        $this->expectExceptionMessage('Password reset was not requested.');
        $user->resetPassword('hash_new', $now);
    }

    private function buildSignedUpByEmailUser(): User
    {
        $user = new User(Id::next(), new DateTimeImmutable());
        $user->requestSignUpByEmail(new Email('test@app.test'), 'hash', 'token');

        return $user;
    }

}
