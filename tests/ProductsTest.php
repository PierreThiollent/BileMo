<?php

namespace App\Tests;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductsTest extends WebTestCase
{
    /**
     * @throws \JsonException
     */
    private function createAuthenticatedClient(): KernelBrowser
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                ['email' => 'pierre.thiollent76@gmail.com', 'password' => 'test'],
                JSON_THROW_ON_ERROR
            )
        );

        $data = json_decode(
            $client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $client->setServerParameter('HTTP_Authorization', "Bearer {$data['token']}");

        return $client;
    }

    public function testGetAllProductsWithoutJWT(): void
    {
        $client = self::createClient();

        $client->request('GET', '/api/products');
        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @throws \JsonException
     */
    public function testGetAllProducts(): void
    {
        $client = $this->createAuthenticatedClient();
        $serializer = static::getContainer()->get('serializer');

        $client->request('GET', '/api/products');
        $response = $client->getResponse();

        self::assertResponseIsSuccessful();
        self::assertJson($response->getContent());

        // Serialize the response content
        $products = $serializer->deserialize(
            $response->getContent(),
            'App\Entity\Product[]',
            'json'
        );

        // check that for each product, it's instance of Product
        foreach ($products as $product) {
            self::assertInstanceOf(Product::class, $product);
        }
    }
}
