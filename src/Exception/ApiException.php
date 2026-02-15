<?php

declare(strict_types=1);

namespace App\Exception;

abstract class ApiException extends \RuntimeException
{
    public function __construct(
        protected ApiErrorCode $errorCode,
        string $message,
        protected int $statusCode,
        protected array $errors = []
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode->value;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
