<?php

declare(strict_types=1);

namespace App\Tests\Controller\V1\User;

use App\Http\ApiRoutes;
use App\Tests\ApiWebTestCase;
use App\Exception\ApiErrorCode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\DataProvider;

final class EditActionTest extends ApiWebTestCase
{
    private const ROOT = 'root';
    private const USER_2 = 'user_2';
    private const INVALID = 'invalid';

    #[DataProvider('updateAccessProvider')]
    public function testUpdateAccessScenarios(
        ?string $tokenType,
        array $payload,
        int $expectedStatus,
        ?string $expectedError,
        ?int $expectedId,
        ?string $expectedMessageContains,
    ): void {
        $token = $this->resolveToken($tokenType);

        $this->jsonRequest(
            Request::METHOD_PUT,
            ApiRoutes::USERS_V1,
            $token,
            $payload
        );

        $this->assertResponseStatusCodeSame($expectedStatus);

        $data = $this->getJsonResponse();

        if ($expectedError !== null) {
            $this->assertSame($expectedError, $data['error']);

            if ($expectedMessageContains !== null) {
                $this->assertStringContainsString(
                    $expectedMessageContains,
                    $data['message'] ?? ''
                );
            }

            return;
        }

        // Success case
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame($expectedId, $data['id']);
    }

    public static function updateAccessProvider(): iterable
    {
        yield 'root updates non-existing user' => [
            'tokenType' => self::ROOT,
            'payload' => [
                'id' => 999999,
                'login' => 'doesNot',
                'phone' => '00000000',
                'pass' => 'nopass',
            ],
            'expectedStatus' => Response::HTTP_NOT_FOUND,
            'expectedError' => ApiErrorCode::NOT_FOUND->value,
            'expectedId' => null,
            'expectedMessageContains' => 'Resource not found',
        ];

        yield 'root updates existing user' => [
            'tokenType' => self::ROOT,
            'payload' => [
                'id' => 2,
                'login' => 'user2RT',
                'phone' => '11111111',
                'pass' => 'newpass2',
            ],
            'expectedStatus' => Response::HTTP_OK,
            'expectedError' => null,
            'expectedId' => 2,
            'expectedMessageContains' => null,
        ];

        yield 'user updates own data' => [
            'tokenType' => self::USER_2,
            'payload' => [
                'id' => 2,
                'login' => 'user1new',
                'phone' => '10000002',
                'pass' => 'pass2',
            ],
            'expectedStatus' => Response::HTTP_OK,
            'expectedError' => null,
            'expectedId' => 2,
            'expectedMessageContains' => null,
        ];

        yield 'user updates another user' => [
            'tokenType' => self::USER_2,
            'payload' => [
                'id' => 1,
                'login' => 'hacker',
                'phone' => '00000000',
                'pass' => 'pass1',
            ],
            'expectedStatus' => Response::HTTP_FORBIDDEN,
            'expectedError' => ApiErrorCode::FORBIDDEN->value,
            'expectedId' => null,
            'expectedMessageContains' => null,
        ];

        yield 'user updates with existing login+pass' => [
            'tokenType' => self::USER_2,
            'payload' => [
                'id' => 2,
                'login' => 'user5',
                'phone' => '12345678',
                'pass' => 'pass5',
            ],
            'expectedStatus' => Response::HTTP_BAD_REQUEST,
            'expectedError' => 'validation_error',
            'expectedId' => null,
            'expectedMessageContains' => 'User already exists',
        ];


        yield 'unauthorized request' => [
            'tokenType' => null,
            'payload' => [],
            'expectedStatus' => Response::HTTP_UNAUTHORIZED,
            'expectedError' => ApiErrorCode::HTTP_ERROR->value,
            'expectedId' => null,
            'expectedMessageContains' => null,
        ];

        yield 'invalid token' => [
            'tokenType' => self::INVALID,
            'payload' => [],
            'expectedStatus' => Response::HTTP_UNAUTHORIZED,
            'expectedError' => ApiErrorCode::UNAUTHORIZED->value,
            'expectedId' => null,
            'expectedMessageContains' => null,
        ];
    }

    #[DataProvider('invalidFieldDataProvider')]
    public function testUpdateWithInvalidFields(array $payload): void
    {
        $this->jsonRequest(
            Request::METHOD_PUT,
            ApiRoutes::USERS_V1,
            $this->asUser(2),
            $payload
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $data = $this->getJsonResponse();
        $this->assertSame('validation_error', $data['error']);
    }

    public static function invalidFieldDataProvider(): iterable
    {
        yield 'missing login' => [[
            'id' => 2,
            'phone' => '10000002',
            'pass' => 'pass2',
        ]];

        yield 'missing phone' => [[
            'id' => 2,
            'login' => 'user1new',
            'pass' => 'pass2',
        ]];

        yield 'missing pass' => [[
            'id' => 2,
            'login' => 'user1new',
            'phone' => '10000002',
        ]];
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
