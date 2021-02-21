<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class Handler
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * Handler constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        $email = mb_strtolower($command->email);

        if ($user = $this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
            throw new \DomainException();
        }

        $user = new User(
            Uuid::uuid4()->toString(),
            $email,
            password_hash($command->password, PASSWORD_DEFAULT),
            new DateTimeImmutable()
        );

        $this->em->persist($user);
        $this->em->flush();
    }
}
