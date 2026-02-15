<?php

declare(strict_types=1);

namespace App\Security;

use App\Enum\UserRole;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

final class TokenAuthenticator extends AbstractAuthenticator
{
    private const ROOT_TOKEN = 'root-token';

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $header = $request->headers->get('Authorization');

        if ($header === null) {
            throw new AuthenticationException('Authorization header missing');
        }

        if (!str_starts_with($header, 'Bearer ')) {
            throw new AuthenticationException('Invalid Authorization header format');
        }

        $token = trim(substr($header, 7));

        return match (true) {
            $token === self::ROOT_TOKEN => $this->rootPassport(),
            preg_match('/^user-(\d+)-token$/', $token, $m) === 1 => $this->userPassport($m[1]),
            default => throw new AuthenticationException('Invalid token'),
        };
    }

    private function rootPassport(): Passport
    {
        return new SelfValidatingPassport(
            new UserBadge(
                'root',
                fn () => new ApiUser(
                    'root',
                    UserRole::ROOT->toArray()
                )
            )
        );
    }

    private function userPassport(string $userId): Passport
    {
        return new SelfValidatingPassport(
            new UserBadge(
                $userId,
                fn () => new ApiUser(
                    $userId,
                    UserRole::USER->toArray()
                )
            )
        );
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): JsonResponse {

        return new JsonResponse(
            [
                'error' => 'unauthorized',
                'message' => $exception->getMessage(),
            ],
            JsonResponse::HTTP_UNAUTHORIZED
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?JsonResponse {
        return null;
    }
}
