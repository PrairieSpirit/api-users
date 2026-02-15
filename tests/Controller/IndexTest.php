<?php

namespace App\Tests\диController;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class IndexTest extends WebTestCase
{
    public function testCanAccessIndexEndpoint(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
        $jsonResult = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($jsonResult['status'], 'ok');
    }
}
