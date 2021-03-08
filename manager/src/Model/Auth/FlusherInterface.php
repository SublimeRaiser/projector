<?php

declare(strict_types=1);

namespace App\Model\Auth;

interface FlusherInterface
{
    public function flush(): void;
}
