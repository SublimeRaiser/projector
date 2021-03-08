<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\ResetPassword\Reset;

class Command
{
    /** @var string */
    public $token;

    /** @var string */
    public $password;
}
