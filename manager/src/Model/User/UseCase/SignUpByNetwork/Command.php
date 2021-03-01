<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUpByNetwork;

class Command
{
    /** @var string */
    public $networkName;

    /** @var string */
    public $identity;
}
