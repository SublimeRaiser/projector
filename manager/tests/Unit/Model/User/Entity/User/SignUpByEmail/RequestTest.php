<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUpByEmail;

use App\Model\User\Entity\User\Email;
use App\Tests\Builder\User\TestUserBuilder;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new TestUserBuilder())
            ->signUpByEmail(
                $email = new Email('test@app.test'),
                $hash  = 'hash',
                $token = 'token'
            )
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        self::assertNotNull($user->getId());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());
        self::assertEquals($token, $user->getConfirmToken());
        self::assertNotNull($user->getDate());

        self::assertTrue($user->getRole()->isUser());
    }
}
