<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUpByEmail;

use App\Tests\Factory\User\UserFactory;
use PHPUnit\Framework\TestCase;

class SignUpTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = UserFactory::buildSignedUpByEmailUser();
        $user->signUpByEmail();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
        self::assertNull($user->getConfirmToken());
    }

    public function testAlreadyConfirmed(): void
    {
        $user = UserFactory::buildSignedUpByEmailUser();
        $user->signUpByEmail();

        $this->expectExceptionMessage('User has already confirmed registration.');
        $user->signUpByEmail();
    }
}
