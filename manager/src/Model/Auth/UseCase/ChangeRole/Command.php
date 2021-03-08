<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\ChangeRole;

class Command
{
    /** @var string */
    public $id;

    /** @var string */
    public $role;
}
