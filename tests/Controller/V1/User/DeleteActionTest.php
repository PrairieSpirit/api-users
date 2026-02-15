<?php

declare(strict_types=1);

namespace App\Tests\Controller\V1\User;

use App\Http\ApiRoutes;
use App\Tests\ApiWebTestCase;
use App\Exception\ApiErrorCode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\DataProvider;

final class DeleteActionTest extends ApiWebTestCase
{
    private const ROOT = 'root';
    private const USER_2 = 'user_2';
    private const INVALID = 'invalid';

    #[DataProvider('deleteUserProvider')]
    public function testDeleteUserScenarios(
        ?string $tokenType,
        ?int $userId,
        int $expectedStatus,
        ?string $expectedError
    ): void {
        $token = $this->resolveToken($tokenType);

        $payload = $userId !== null ? ['id' => $userId] : [];

        $this->jsonRequest(Request::METHOD_DELETE, ApiRoutes::USERS_V1, $token, $payload);

        $this->assertResponseStatusCodeSame($expectedStatus);

        $data = $this->getJsonResponse();

        if ($expectedError !== null) {
            $this->assertSame($expectedError, $data['error']);
        } else {
            $this->assertSame(['status' => 'DELETE'], $data);
        }
    }

    public static function deleteUserProvider(): iterable
    {
        yield 'root deletes existing user' => [
            'tokenType' => self::ROOT,
            'userId' => 2,
            'expectedStatus' => Response::HTTP_OK,
            'expectedError' => null,
        ];

        yield 'root deletes non-existing user' => [
            'tokenType' => self::ROOT,
            'userId' => 9999,
            'expectedStatus' => Response::HTTP_NOT_FOUND,
            'expectedError' => ApiErrorCode::NOT_FOUND->value,
        ];

        yield 'missing id' => [
            'tokenType' => self::ROOT,
            'userId' => null,
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedError' => ApiErrorCode::HTTP_ERROR->value,
        ];

        yield 'unauthorized request' => [
            'tokenType' => null,
            'userId' => 2,
            'expectedStatus' => Response::HTTP_UNAUTHORIZED,
            'expectedError' => ApiErrorCode::HTTP_ERROR->value,
        ];

        yield 'invalid token' => [
            'tokenType' => self::INVALID,
            'userId' => 2,
            'expectedStatus' => Response::HTTP_UNAUTHORIZED,
            'expectedError' => ApiErrorCode::UNAUTHORIZED->value,
        ];

        yield 'user tries to delete another user' => [
            'tokenType' => self::USER_2,
            'userId' => 1,
            'expectedStatus' => Response::HTTP_FORBIDDEN,
            'expectedError' => ApiErrorCode::FORBIDDEN->value,
        ];
    }

    private function resolveToken(?string $tokenType): ?string
    {
        return match ($tokenType) {
            self::ROOT => $this->asRoot(),
            self::USER_2 => $this->asUser(2),
            self::INVALID => $this->asInvalid(),
            null => null,
            default => throw new \InvalidArgumentException(
                sprintf('Unknown token type "%s"', $tokenType)
            ),
        };
    }
}
