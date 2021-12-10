<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private PaginationService   $pagination,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function index(ProductRepository $repository, Request $request): JsonResponse
    {
        $pagination = $this->pagination->paginate(
            $repository,
            'api_get_products',
            $request->query->getInt('page', 1)
        );

        return new JsonResponse(
            $this->serializer->serialize($pagination, 'json'),
            Response::HTTP_PARTIAL_CONTENT,
            ['Content-Type' => 'application/hal+json'],
            true
        );
    }

    public function show(Product $product, Request $request): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($product, 'json'),
            Response::HTTP_OK,
            ['Content-Type' => 'application/hal+json'],
            true
        );
    }
}
