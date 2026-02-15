<?php

declare(strict_types=1);

namespace App\Tests\Controller\V1\User;

use App\Http\ApiRoutes;
use App\Tests\ApiWebTestCase;
use App\Exception\ApiErrorCode;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetActionTest extends ApiWebTestCase
{
    private const ROOT = 'root';
    private const USER_2 = 'user_2';
    private const INVALID = 'invalid';

    #[DataProvider('getAccessProvider')]
    public function testGetAccessScenarios(
        ?string $tokenType,
        ?int $userId,
        int $expectedStatus,
        ?string $expectedError,
        ?array $expectedData
    ): void {
        $token = $this->resolveToken($tokenType);

        $uri = ApiRoutes::USERS_V1;
        if ($userId !== null) {
            $uri .= '?id=' . $userId;
        }

        $this->jsonRequest(Request::METHOD_GET, $uri, $token);

        $this->assertResponseStatusCodeSame($expectedStatus);

        $data = $this->getJsonResponse();

        if ($expectedError !== null) {
            $this->assertSame($expectedError, $data['error']);
            return;
        }

        $this->assertSame($expectedData, array_values($data));
    }

    public static function getAccessProvider(): iterable
    {
        yield 'root gets existing user' => [
            'tokenType' => self::ROOT,
            'userId' => 2,
            'expectedStatus' => Response::HTTP_OK,
            'expectedError' => null,
            'expectedData' => ['user1', '09900001', 'pass1'],
        ];

        yield 'user gets own data' => [
            'tokenType' => self::USER_2,
            'userId' => 2,
            'expectedStatus' => Response::HTTP_OK,
            'expectedError' => null,
            'expectedData' => ['user1', '09900001', 'pass1'],
        ];

        yield 'user gets another user' => [
            'tokenType' => self::USER_2, // id = 2
            'userId' => 1,
            'expectedStatus' => Response::HTTP_FORBIDDEN,
            'expectedError' => ApiErrorCode::FORBIDDEN->value,
            'expectedData' => null,
        ];

        yield 'unauthorized request' => [
            'tokenType' => null,
            'userId' => 2,
            'expectedStatus' => Response::HTTP_UNAUTHORIZED,
            'expectedError' => ApiErrorCode::HTTP_ERROR->value,
            'expectedData' => null,
        ];

        yield 'invalid token' => [
            'tokenType' => self::INVALID,
            'userId' => 2,
            'expectedStatus' => Response::HTTP_UNAUTHORIZED,
            'expectedError' => ApiErrorCode::UNAUTHORIZED->value,
            'expectedData' => null,
        ];

        yield 'missing id' => [
            'tokenType' => self::ROOT,
            'userId' => null,
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedError' => ApiErrorCode::HTTP_ERROR->value,
            'expectedData' => null,
        ];

        yield 'non-existing user' => [
            'tokenType' => self::ROOT,
            'userId' => 9999,
            'expectedStatus' => Response::HTTP_NOT_FOUND,
            'expectedError' => ApiErrorCode::NOT_FOUND->value,
            'expectedData' => null,
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
