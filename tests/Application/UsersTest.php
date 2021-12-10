<?php

namespace App\Tests\Application;

use App\Entity\User;

class UsersTest extends AbstractTest
{
    public function testGetUsersWithoutTokenShouldReturn401(): void
    {
        $this->client->request('GET', '/api/users');
        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @throws \JsonException
     */
    public function testGetUsersShouldReturnArrayOfUsers(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('GET', '/api/users');
        $response = $this->client->getResponse();

        self::assertResponseIsSuccessful();
        self::assertJson($response->getContent());
        self::assertResponseHasHeader('Content-Type', 'application/hal+json');

        $data = json_decode(
            $response->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $users = $this->serializer->deserialize(
            json_encode($data['_embedded'], JSON_THROW_ON_ERROR),
            'array<App\Entity\User>',
            'json',
        );

        foreach ($users as $user) {
            self::assertInstanceOf(User::class, $user);
        }
    }

    /**
     * @throws \JsonException
     */
    public function testGetUsersShouldReturnJsonLdData(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('GET', '/api/users');
        $response = $this->client->getResponse();

        $response = json_decode(
            $response->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertArrayHasKey('page', $response);
        self::assertArrayHasKey('limit', $response);
        self::assertArrayHasKey('pages', $response);
        self::assertArrayHasKey('total', $response);

        self::assertArrayHasKey('_links', $response);
        self::assertArrayHasKey('self', $response['_links']);
        self::assertArrayHasKey('href', $response['_links']['self']);

        self::assertArrayHasKey('first', $response['_links']);
        self::assertArrayHasKey('last', $response['_links']);
    }
}
