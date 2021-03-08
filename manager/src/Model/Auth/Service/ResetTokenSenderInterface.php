<?php

declare(strict_types=1);

namespace App\Model\Auth\Service;

use App\Model\Auth\Entity\User\Email;
use App\Model\Auth\Entity\User\ResetToken;

interface ResetTokenSenderInterface
{
    public function send(Email $email, ResetToken $token): void;
}
