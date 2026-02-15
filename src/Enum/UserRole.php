<?php

declare(strict_types=1);

namespace App\Enum;

enum UserRole: string
{
    case ROOT = 'ROLE_ROOT';
    case USER = 'ROLE_USER';

    public function toArray(): array
    {
        return [$this->value];
    }
}
