<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\ResetPassword\Reset;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $token;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public $password;

    /**
     * Command constructor.
     *
     * @param string      $token
     * @param string|null $password
     */
    public function __construct(string $token, string $password = null)
    {
        $this->token    = $token;
        $this->password = $password;
    }
}
