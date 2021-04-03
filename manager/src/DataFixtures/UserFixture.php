<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Auth\Entity\User\Email;
use App\Model\Auth\Entity\User\Id;
use App\Model\Auth\Entity\User\Role;
use App\Model\Auth\Entity\User\User;
use App\Model\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    /**
     * @var PasswordHasher
     */
    private $hasher;

    /**
     * UserFixture constructor.
     *
     * @param PasswordHasher $hasher
     */
    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $hash = $this->hasher->hash('password');

        $user = User::signUpByEmail(
            Id::next(),
            new DateTimeImmutable(),
            new Email('admin@app.test'),
            $hash,
            'token'
        );
        $user->confirmSignUpByEmail();
        $user->changeRole(Role::admin());
        $manager->persist($user);
        $manager->flush();
    }
}
