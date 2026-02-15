<?php

declare(strict_types=1);

namespace App\Exception;

final class ApiErrorMessage
{
    public const UNAUTHORIZED = 'Unauthorized';
    public const FORBIDDEN = 'Forbidden';
    public const HTTP_ERROR = 'HTTP error';
    public const INTERNAL_ERROR = 'Internal server error';
}
