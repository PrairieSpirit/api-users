<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

final class NotFoundException extends ApiException
{
    public function __construct(string $message = 'Resource not found')
    {
        parent::__construct(
            ApiErrorCode::NOT_FOUND,
            $message,
            Response::HTTP_NOT_FOUND
        );
    }
}
