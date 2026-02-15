<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

final class UnauthorizedException extends ApiException
{
    public function __construct(string $message = 'Unauthorized')
    {
        parent::__construct(
            ApiErrorCode::UNAUTHORIZED,
            $message,
            Response::HTTP_UNAUTHORIZED
        );
    }
}
