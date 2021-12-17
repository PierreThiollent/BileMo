<?php

namespace App\Tests\Functional;

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

        self::assertResponseStatusCodeSame(206);
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

    public function testGetOneUserWithoutTokenShouldReturn401(): void
    {
        $this->client->request('GET', '/api/user/1');
        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @throws \JsonException
     */
    public function testGetOneUserShouldReturnUser(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('GET', '/api/user/2');
        $response = $this->client->getResponse();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($response->getContent());
        self::assertResponseHasHeader('Content-Type', 'application/hal+json');

        $user = $this->serializer->deserialize(
            $response->getContent(),
            User::class,
            'json',
        );

        self::assertInstanceOf(User::class, $user);
    }

    /**
     * @throws \JsonException
     */
    public function testGetOneUserShouldReturnJsonLdData(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('GET', '/api/user/2');
        $response = $this->client->getResponse();

        $response = json_decode(
            $response->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertArrayHasKey('_links', $response);
        self::assertArrayHasKey('self', $response['_links']);
        self::assertArrayHasKey('href', $response['_links']['self']);

        self::assertArrayHasKey('next', $response['_links']);
        self::assertArrayHasKey('href', $response['_links']['next']);

        self::assertArrayHasKey('first', $response['_links']);
        self::assertArrayHasKey('href', $response['_links']['first']);

        self::assertArrayHasKey('last', $response['_links']);
        self::assertArrayHasKey('href', $response['_links']['last']);
    }

    public function testDeleteOneUserWithoutTokenShouldReturn401(): void
    {
        $this->client->request('DELETE', '/api/user/1');
        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @throws \JsonException
     */
    public function testDeleteOneUserShouldReturn200(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('DELETE', '/api/user/2');
        self::assertResponseStatusCodeSame(200);
        self::assertJson($this->client->getResponse()->getContent());

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertArrayHasKey('message', $response);
        self::assertSame('L\'utilisateur a bien été supprimé', $response['message']);
    }

    /**
     * @throws \JsonException
     */
    public function testDeleteUserOfAnotherClientShouldReturn403(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('DELETE', '/api/user/5');
        self::assertResponseStatusCodeSame(403);

        $response = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertArrayHasKey('message', $response);
        self::assertSame('You are not allowed to delete this user', $response['message']);

        self::assertArrayHasKey('code', $response);
        self::assertSame(403, $response['code']);
    }
}
