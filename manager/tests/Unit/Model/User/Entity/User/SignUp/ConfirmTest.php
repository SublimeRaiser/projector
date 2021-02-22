<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = $this->buildSignedUpUser();
        $user->confirmSignUp();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
        self::assertNull($user->getConfirmToken());
    }

    public function testAlreadyConfirmed(): void
    {
        $user = $this->buildSignedUpUser();
        $user->confirmSignUp();

        $this->expectExceptionMessage('User has already confirmed registration.');
        $user->confirmSignUp();
    }

    private function buildSignedUpUser(): User
    {
        return new User(
            $id    = Id::next(),
            $email = new Email('test@app.test'),
            $hash  = 'hash',
            $token = 'token',
            $date  = new DateTimeImmutable()
        );
    }
}
