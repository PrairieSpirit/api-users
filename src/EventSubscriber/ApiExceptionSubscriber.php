<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\ApiException;
use App\Exception\ApiErrorCode;
use App\Exception\ApiErrorMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        [$status, $error, $message, $errors] = match (true) {

            $throwable instanceof ApiException => [
                $throwable->getStatusCode(),
                $throwable->getErrorCode(),
                $throwable->getMessage(),
                $throwable->getErrors(),
            ],

            $throwable instanceof AuthenticationException => [
                Response::HTTP_UNAUTHORIZED,
                ApiErrorCode::UNAUTHORIZED->value,
                ApiErrorMessage::UNAUTHORIZED,
                [],
            ],

            $throwable instanceof AccessDeniedException => [
                Response::HTTP_FORBIDDEN,
                ApiErrorCode::FORBIDDEN->value,
                ApiErrorMessage::FORBIDDEN,
                [],
            ],

            $throwable instanceof HttpExceptionInterface => [
                $throwable->getStatusCode(),
                ApiErrorCode::HTTP_ERROR->value,
                $throwable->getMessage() ?: ApiErrorMessage::HTTP_ERROR,
                [],
            ],

            default => [
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ApiErrorCode::INTERNAL_ERROR->value,
//                ApiErrorMessage::INTERNAL_ERROR, // Use for debug $throwable->getMessage(),
                $throwable->getMessage(),
                [],
            ],

        };

        $event->setResponse(
            $this->json($status, $error, $message, $errors)
        );
    }

    private function json(int $status, string $error, string $message, array $errors): JsonResponse
    {
        return new JsonResponse(
            [
                'error' => $error,
                'message' => $message,
                'status' => $status,
                'errors' => $errors,
            ],
            $status
        );
    }
}
