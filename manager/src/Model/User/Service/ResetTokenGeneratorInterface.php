<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\ResetToken;

interface ResetTokenGeneratorInterface
{
    public function generate(): ResetToken;
}
