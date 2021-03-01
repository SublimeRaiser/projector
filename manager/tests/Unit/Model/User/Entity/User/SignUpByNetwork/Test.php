<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SignUpByNetwork;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testSuccess(): void
    {
        $user = $this->buildSignedUpByNetworkUser();

        self::assertTrue($user->isActive());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals('vk', $first->getNetworkName());
        self::assertEquals('0000001', $first->getIdentity());
    }

    public function testAlreadySignedUp(): void
    {
        $user = $this->buildSignedUpByNetworkUser();

        $this->expectExceptionMessage('User is already signed up.');
        $user->signUpByNetwork('vk', '0000001');
    }

    private function buildSignedUpByNetworkUser(): User
    {
        $user = new User(Id::next(), new DateTimeImmutable());
        $user->signUpByNetwork('vk', '0000001');

        return $user;
    }
}
