<?php

declare(strict_types=1);

namespace App\Model\User;

interface FlusherInterface
{
    public function flush(): void;
}
