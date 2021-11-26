<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    /**
     * @throws \JsonException
     */
    public function testAuthWithWrongCredentials(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], '{"email":"admin","password":"admin"}');

        self::assertResponseStatusCodeSame(401);
        $response = $client->getResponse();
        self::assertJson($response->getContent());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('code', $data);
        self::assertArrayHasKey('message', $data);

        self::assertEquals('Invalid credentials.', $data['message']);
    }

    /**
     * @throws \JsonException
     */
    public function testAuthWithValidCredentials(): void
    {
        $client = static::createClient();

        $client->request('POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"email":"pierre.thiollent76@gmail.com","password":"test"}'
        );

        self::assertResponseIsSuccessful();
        $response = $client->getResponse();

        self::assertJson($response->getContent());

        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('token', $data);
        self::assertIsString($data['token']);
    }
}
