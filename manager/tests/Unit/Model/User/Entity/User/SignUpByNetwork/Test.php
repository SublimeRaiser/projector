<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUpByNetwork;

use App\Model\User\Entity\User\Network;
use App\Tests\Factory\User\UserFactory;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testSuccess(): void
    {
        $user = UserFactory::buildSignedUpByNetworkUser();

        self::assertTrue($user->isActive());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals('vk', $first->getNetworkName());
        self::assertEquals('0000001', $first->getIdentity());
    }

    public function testAlreadySignedUp(): void
    {
        $user = UserFactory::buildSignedUpByNetworkUser();

        $this->expectExceptionMessage('User is already signed up.');
        $user->signUpByNetwork('vk', '0000001');
    }
}
