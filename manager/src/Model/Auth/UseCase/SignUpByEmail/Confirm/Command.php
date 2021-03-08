<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\SignUpByEmail\Confirm;

class Command
{
    /** @var string */
    public $token;

    /**
     * Command constructor.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }
}
