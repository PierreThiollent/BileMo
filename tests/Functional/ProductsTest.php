<?php

namespace App\Tests\Functional;

use App\Entity\Product;
use JsonException;

class ProductsTest extends AbstractTest
{
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
    }

    public function testGetOneProductWithoutAuthenticationShouldReturn401(): void
    {
        $this->client->request('GET', '/api/product/1');
        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @throws JsonException
     */
    public function testGetOneProductShouldReturnProduct(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('GET', '/api/product/1');
        $response = $this->client->getResponse();

        self::assertResponseStatusCodeSame(200);
        self::assertJson($response->getContent());
        self::assertResponseHasHeader('Content-Type', 'application/hal+json');

        $product = $this->serializer->deserialize(
            $response->getContent(),
            Product::class,
            'json',
        );

        self::assertInstanceOf(Product::class, $product);
    }

    /**
     * @throws JsonException
     */
    public function testGetOneProductShouldContainsJsonLdData(): void
    {
        $this->client = $this->createAuthenticatedClient();

        $this->client->request('GET', '/api/product/1');
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
    }
}
