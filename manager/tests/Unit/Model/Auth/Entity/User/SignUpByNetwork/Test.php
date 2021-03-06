<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Auth\Entity\User\SignUpByNetwork;

use App\Model\Auth\Entity\User\Network;
use App\Tests\Builder\User\TestUserBuilder;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new TestUserBuilder())->signUpByNetwork()->build();

        self::assertTrue($user->isActive());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals('vk', $first->getNetworkName());
        self::assertEquals('0000001', $first->getIdentity());

        self::assertTrue($user->getRole()->isUser());
    }
}
