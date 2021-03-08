<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\ResetPassword\Request;

class Command
{
    /** @var string */
    public $email;
}
