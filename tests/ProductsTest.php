<?php

namespace App\Tests;

use App\Entity\Product;
use JMS\Serializer\Serializer;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductsTest extends WebTestCase
{
    private KernelBrowser $client;
    private Serializer $serializer;

    public function testGetAllProductsWithoutJWTShouldReturn401(): void
    {
        $this->client->request('GET', '/api/products');
        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @throws JsonException
     */
    public function testGetAllProductsShouldReturnArrayOfProducts(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('GET', '/api/products');
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

        $products = $this->serializer->deserialize(
            json_encode($data['_embedded'], JSON_THROW_ON_ERROR),
            'array<App\Entity\Product>',
            'json',
        );

        foreach ($products as $product) {
            self::assertInstanceOf(Product::class, $product);
        }
    }

    /**
     * @throws JsonException
     */
    private function createAuthenticatedClient(): KernelBrowser
    {
        $this->client->request(
            'POST',
            '/api/login',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{"email":"pierre.thiollent76@gmail.com","password":"test"}'
        );

        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->client->setServerParameter('HTTP_Authorization', "Bearer {$data['token']}");

        return $this->client;
    }

    /**
     * @throws JsonException
     */
    public function testGetAllProductsShouldReturnJsonLdData(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('GET', '/api/products');
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

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->serializer = static::getContainer()->get('jms_serializer');
    }
}
