<?php

declare(strict_types=1);

namespace App\ReadModel\Auth;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class UserFetcher
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * UserFetcher constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    public function existsByResetToken(string $token): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('user_user')
            ->where('reset_token_value = :token')
            ->setParameter('token', $token)
            ->execute()
            ->fetchColumn(0) > 0;
    }

    /**
     * @param string $email
     *
     * @return AuthView
     */
    public function findForAuth(string $email): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'email', 'password_hash', 'role', 'status')
            ->from('user_user')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }
}
