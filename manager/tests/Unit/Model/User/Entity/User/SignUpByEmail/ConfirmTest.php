<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUpByEmail;

use App\Tests\Builder\User\TestUserBuilder;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new TestUserBuilder())->requestSignUpByEmail()->confirmSignUpByEmail()->build();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
        self::assertNull($user->getConfirmToken());
    }

    public function testAlreadyConfirmed(): void
    {
        $user = (new TestUserBuilder())->requestSignUpByEmail()->confirmSignUpByEmail()->build();

        $this->expectExceptionMessage('User has already confirmed registration.');
        $user->confirmSignUpByEmail();
    }

    public function testNotRequested(): void
    {
        $user = (new TestUserBuilder())->build();

        $this->expectExceptionMessage('Sign up was not requested.');
        $user->confirmSignUpByEmail();
    }
}
