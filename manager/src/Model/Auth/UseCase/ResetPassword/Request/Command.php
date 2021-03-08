<?php

declare(strict_types=1);

namespace App\Model\Auth\UseCase\ResetPassword\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;
}
