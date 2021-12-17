<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private PaginationService $pagination,
    ) {
    }

    public function index(UserRepository $repository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $pagination = $this->pagination->paginate(
            count($repository->findAll()),
            $repository->findBy(['client' => $this->getUser()], limit: 5, offset: $page * 5 - 5),
            'api_get_users',
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

    public function show(User $user): JsonResponse
    {
        $context = SerializationContext::create()
            ->setGroups([
                'Default',
                'detail',
            ]);

        return new JsonResponse(
            $this->serializer->serialize($user, 'json'),
            Response::HTTP_OK,
            ['Content-Type' => 'application/hal+json'],
            true
        );
    }
}
