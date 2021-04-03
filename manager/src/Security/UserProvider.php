<?php

declare(strict_types=1);

namespace App\Security;

use App\ReadModel\Auth\AuthView;
use App\ReadModel\Auth\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserFetcher
     */
    private $users;

    /**
     * UserProvider constructor.
     *
     * @param UserFetcher $users
     */
    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->loadUser($username);

        return self::buildIdentityByUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $identity): UserInterface
    {
        if (!$identity instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class '.get_class($identity));
        }
        $user = $this->loadUser($identity->getUsername());

        return self::buildIdentityByUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class): bool
    {
        return $class === UserIdentity::class;
    }

    /**
     * Loads user data from the database.
     *
     * @param string $username
     *
     * @return AuthView
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    private function loadUser(string $username): AuthView
    {
        $user = $this->users->findForAuth($username);
        if (!$user) {
            throw new UsernameNotFoundException('User not found.');
        }

        return $user;
    }

    /**
     * Builds user identity for the provided user data.
     *
     * @param AuthView $user
     *
     * @return UserIdentity
     */
    private static function buildIdentityByUser(AuthView $user): UserIdentity
    {
        return new UserIdentity(
            $user->id,
            $user->email,
            $user->password_hash,
            $user->role,
            $user->status
        );
    }
}
