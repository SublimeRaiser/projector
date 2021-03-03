<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\ChangeRole;

use App\Model\User\Entity\User\Role;
use App\Tests\Builder\User\TestUserBuilder;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();
        $user->changeRole(Role::admin());

        Assert::assertFalse($user->getRole()->isUser());
        Assert::assertTrue($user->getRole()->isAdmin());
    }

    public function testAlreadyAssigned(): void
    {
        $user = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

        $this->expectExceptionMessage('This role has already been assigned.');
        $user->changeRole(Role::user());
    }
}
