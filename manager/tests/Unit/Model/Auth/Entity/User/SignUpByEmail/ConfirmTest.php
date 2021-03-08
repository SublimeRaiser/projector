<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Auth\Entity\User\SignUpByEmail;

use App\Tests\Builder\User\TestUserBuilder;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
        self::assertNull($user->getConfirmToken());
    }

    public function testAlreadyConfirmed(): void
    {
        $user = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

        $this->expectExceptionMessage('User has already confirmed registration.');
        $user->confirmSignUpByEmail();
    }
}
