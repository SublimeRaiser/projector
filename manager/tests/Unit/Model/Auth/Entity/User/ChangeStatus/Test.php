<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Auth\Entity\User\ChangeStatus;

use App\Model\Auth\Entity\User\Status;
use App\Tests\Builder\User\TestUserBuilder;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();
        $user->changeStatus(Status::wait());

        Assert::assertFalse($user->isNew());
        Assert::assertTrue($user->isWait());
    }

    public function testAlreadyAssigned(): void
    {
        $user = (new TestUserBuilder())->signUpByEmail()->confirmSignUpByEmail()->build();

        $this->expectExceptionMessage('This status has already been assigned.');
        $user->changeStatus(Status::active());
    }
}
