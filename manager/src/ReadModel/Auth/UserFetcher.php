<?php

declare(strict_types=1);

namespace App\ReadModel\Auth;

use Doctrine\DBAL\Connection;

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
}
