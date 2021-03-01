<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUpByEmail;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SignUpTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = $this->buildSignedUpByEmailUser();
        $user->signUpByEmail();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
        self::assertNull($user->getConfirmToken());
    }

    public function testAlreadyConfirmed(): void
    {
        $user = $this->buildSignedUpByEmailUser();
        $user->signUpByEmail();

        $this->expectExceptionMessage('User has already confirmed registration.');
        $user->signUpByEmail();
    }

    private function buildSignedUpByEmailUser(): User
    {
        $user = new User(Id::next(), new DateTimeImmutable());
        $user->requestSignUpByEmail(new Email('test@app.test'), 'hash', 'token');

        return $user;
    }
}
