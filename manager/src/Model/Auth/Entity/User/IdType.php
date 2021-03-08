<?php

declare(strict_types=1);

namespace App\Model\Auth\Entity\User;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class IdType extends StringType
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? new Id($value) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_user_id';
    }
}
