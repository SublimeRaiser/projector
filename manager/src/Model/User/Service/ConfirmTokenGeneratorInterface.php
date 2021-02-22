<?php

declare(strict_types=1);

namespace App\Model\User\Service;

interface ConfirmTokenGeneratorInterface
{
    public function generate(): string;
}
