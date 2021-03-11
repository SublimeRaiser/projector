<?php

declare(strict_types=1);

namespace App\Model\Auth\Entity\User;

use App\Model\Auth\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

class UserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /** @var EntityRepository */
    private $repo;

    /**
     * UserRepository constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em   = $em;
        $this->repo = $em->getRepository(User::class);
    }

    /**
     * @param string $token
     *
     * @return User|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        /** @var User|null $user */
        $user = $this->repo->findOneBy(['confirmToken' => $token]);

        return $user;
    }

    /**
     * @param string $token
     *
     * @return User|null
     */
    public function findByResetToken(string $token): ?User
    {
        /** @var User|null $user */
        $user = $this->repo->findOneBy(['resetToken.value' => $token]);

        return $user;
    }

    /**
     * @param Email $email
     *
     * @return User
     *
     * @throws EntityNotFoundException If user is not found.
     */
    public function getByEmail(Email $email): User
    {
        /** @var User|null $user */
        $user = $this->repo->findBy(['email' => $email->getValue()]);
        if (!$user) {
            throw new EntityNotFoundException('User is not found.');
        }

        return $user;
    }

    /**
     * @param Id $id
     *
     * @return User
     *
     * @throws EntityNotFoundException If user is not found.
     */
    public function getById(Id $id): User
    {
        /** @var User|null $user */
        $user = $this->repo->find($id->getValue());
        if (!$user) {
            throw new EntityNotFoundException('User is not found.');
        }

        return $user;
    }

    /**
     * @param Email $email
     *
     * @return bool
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function existsByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('uu')
            ->select('COUNT(uu.id)')
            ->andWhere('uu.email = :email')
            ->setParameter('email', $email->getValue())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * @param string $networkName
     * @param string $identity
     *
     * @return bool
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function existsByNetwork(string $networkName, string $identity): bool
    {
        return $this->repo->createQueryBuilder('uu')
            ->select('COUNT(uu.id)')
            ->leftJoin('uu.networks', 'n')
            ->andWhere('n.network_name = :networkName AND n.identity = :identity')
            ->setParameter('networkName', $networkName)
            ->setParameter('identity', $identity)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * @param User $user
     */
    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}
