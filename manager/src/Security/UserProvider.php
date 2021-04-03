<?php

declare(strict_types=1);

namespace App\Security;

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
        $user = $this->users->findForAuth($username);
        if (!$user) {
            throw new UsernameNotFoundException('User not found.');
        }

        return new UserIdentity(
            $user->id,
            $user->email,
            $user->password_hash,
            $user->role,
            $user->status
        );

    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedUserException('Invalid user class '.get_class($user));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class): bool
    {
        return $class === UserIdentity::class;
    }
}
