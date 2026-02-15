<?php

declare(strict_types=1);

namespace App\Tests\Controller\V1\User;

use App\Http\ApiRoutes;
use App\Tests\ApiWebTestCase;
use App\Exception\ApiErrorCode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\DataProvider;

final class CreateActionTest extends ApiWebTestCase
{
    private const ROOT = 'root';
    private const USER_2 = 'user_2';
    private const INVALID = 'invalid';

    #[DataProvider('createUserProvider')]
    public function testCreateUserScenarios(
        ?string $tokenType,
        array $payload,
        int $expectedStatus,
        ?string $expectedError,
        ?array $expectedData
    ): void {
        $token = $this->resolveToken($tokenType);

        $this->jsonRequest(Request::METHOD_POST, ApiRoutes::USERS_V1, $token, $payload);

        $this->assertResponseStatusCodeSame($expectedStatus);

        $data = $this->getJsonResponse();

        if ($expectedError !== null) {
            $this->assertSame($expectedError, $data['error']);
            return;
        }

        $this->assertArrayHasKey('id', $data);
        $this->assertSame($expectedData['login'], $data['login']);
        $this->assertSame($expectedData['phone'], $data['phone']);
        $this->assertSame($expectedData['pass'], $data['pass']);
    }

    public static function createUserProvider(): iterable
    {
        yield 'create new user successfully' => [
            'tokenType' => self::ROOT,
            'payload' => [
                'login' => 'newuser',
                'phone' => '12345678',
                'pass' => 'newpass',
            ],
            'expectedStatus' => Response::HTTP_CREATED,
            'expectedError' => null,
            'expectedData' => [
                'login' => 'newuser',
                'phone' => '12345678',
                'pass' => 'newpass',
            ],
        ];

        yield 'missing login' => [
            'tokenType' => self::ROOT,
            'payload' => [
                'phone' => '12345678',
                'pass' => 'newpass',
            ],
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedError' => 'validation_error',
            'expectedData' => null,
        ];

        yield 'missing phone' => [
            'tokenType' => self::ROOT,
            'payload' => [
                'login' => 'newuser',
                'pass' => 'newpass',
            ],
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedError' => 'validation_error',
            'expectedData' => null,
        ];

        yield 'missing pass' => [
            'tokenType' => self::ROOT,
            'payload' => [
                'login' => 'newuser',
                'phone' => '12345678',
            ],
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedError' => 'validation_error',
            'expectedData' => null,
        ];

        yield 'duplicate login+pass' => [
            'tokenType' => self::ROOT,
            'payload' => [
                'login' => 'user1',
                'phone' => '09999999',
                'pass' => 'pass1',
            ],
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedError' => 'validation_error',
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
