<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

final class ForbiddenException extends ApiException
{
    public function __construct(string $message = 'Forbidden')
    {
        parent::__construct(
            ApiErrorCode::FORBIDDEN,
            $message,
            Response::HTTP_FORBIDDEN
        );
    }
}
