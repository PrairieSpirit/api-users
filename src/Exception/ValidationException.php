<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationException extends ApiException
{
    public function __construct(
        string $message = 'Validation failed',
        array $errors = []
    ) {
        parent::__construct(
            ApiErrorCode::VALIDATION_ERROR,
            $message,
            Response::HTTP_BAD_REQUEST,
            $errors
        );
    }

    public static function fromViolations(
        ConstraintViolationListInterface $violations
    ): self {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }
        return new self('Validation failed', $errors);
    }
}
