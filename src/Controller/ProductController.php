<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use JMS\Serializer\SerializationContext;
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
        private PaginationService $pagination,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function index(ProductRepository $repository, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);

        $pagination = $this->pagination->paginate(
            count($repository->findAll()),
            $repository->findBy([], limit: 5, offset: $page * 5 - 5),
            'api_get_products',
            $page
        );

        $context = SerializationContext::create()
            ->setGroups([
                'Default',
                'list',
            ]);

        return new JsonResponse(
            $this->serializer->serialize($pagination, 'json', $context),
            Response::HTTP_PARTIAL_CONTENT,
            ['Content-Type' => 'application/hal+json'],
            true
        );
    }

    public function show(Product $product): JsonResponse
    {
        $context = SerializationContext::create()
            ->setGroups([
                'Default',
                'detail',
            ]);

        return new JsonResponse(
            $this->serializer->serialize($product, 'json', $context),
            Response::HTTP_OK,
            ['Content-Type' => 'application/hal+json'],
            true
        );
    }
}
