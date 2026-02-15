<?php

declare(strict_types=1);

namespace App\Tests;

use App\DataFixtures\UserFixtures;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiWebTestCase extends WebTestCase
{
    /* =========================
     *  HTTP / API constants
     * ========================= */

    protected const JSON_MIME = 'application/json';

    protected const HEADER_CONTENT_TYPE = 'CONTENT_TYPE';
    protected const HEADER_ACCEPT = 'HTTP_ACCEPT';
    protected const HEADER_AUTHORIZATION = 'HTTP_AUTHORIZATION';

    protected const AUTH_SCHEME_BEARER = 'Bearer';

    /* =========================
     *  Test tokens
     * ========================= */

    protected const ROOT_TOKEN = 'root-token';
    protected const INVALID_TOKEN = 'invalid-token';

    /* =========================
     *  DB
     * ========================= */

    protected const TABLE_USERS = 'users';

    protected KernelBrowser $client;
    protected Connection $connection;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = self::getContainer();

        $this->em = $container->get('doctrine')->getManager();
        $this->connection = $this->em->getConnection();

        $this->resetDatabase();
    }

    /* =========================
     *  Database helpers
     * ========================= */

    protected function resetDatabase(): void
    {
        $this->truncateTable(self::TABLE_USERS);
        $this->loadFixtures();
    }

    protected function truncateTable(string $tableName): void
    {
        $platform = $this->connection->getDatabasePlatform();

        $this->connection->executeStatement(
            $platform->getTruncateTableSQL($tableName, true)
        );
    }

    protected function loadFixtures(): void
    {
        $fixture = new UserFixtures();
        $fixture->load($this->em);
    }

    /* =========================
     *  HTTP helpers
     * ========================= */

    protected function jsonRequest(
        string $method,
        string $uri,
        ?string $token = null,
        array $payload = []
    ): void {
        $this->client->request(
            $method,
            $uri,
            server: $this->buildJsonHeaders($token),
            content: $payload !== []
                ? json_encode($payload, JSON_THROW_ON_ERROR)
                : null
        );
    }

    protected function buildJsonHeaders(?string $token = null): array
    {
        $headers = [
            self::HEADER_CONTENT_TYPE => self::JSON_MIME,
            self::HEADER_ACCEPT => self::JSON_MIME,
        ];

        if ($token !== null) {
            $headers[self::HEADER_AUTHORIZATION] =
                self::AUTH_SCHEME_BEARER . ' ' . $token;
        }

        return $headers;
    }

    protected function getJsonResponse(): array
    {
        return json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /* =========================
     *  Auth helpers
     * ========================= */

    protected function asUser(int $id): string
    {
        return sprintf('user-%d-token', $id);
    }

    protected function asRoot(): string
    {
        return self::ROOT_TOKEN;
    }

    protected function asInvalid(): string
    {
        return self::INVALID_TOKEN;
    }
}
