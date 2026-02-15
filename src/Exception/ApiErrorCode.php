<?php

declare(strict_types=1);

namespace App\Exception;

enum ApiErrorCode: string
{
    case VALIDATION_ERROR = 'validation_error';
    case NOT_FOUND = 'not_found';
    case FORBIDDEN = 'forbidden';
    case UNAUTHORIZED = 'unauthorized';
    case HTTP_ERROR = 'http_error';
    case INTERNAL_ERROR = 'internal_error';
}
