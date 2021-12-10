<?php

namespace App\Service;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

class PaginationService
{
    public function paginate($repository, string $route, int $page): PaginatedRepresentation
    {
        $items = $repository->findAll();

        return new PaginatedRepresentation(
            new CollectionRepresentation($repository->findBy([], offset: $page * 10 - 10, limit: 10)),
            $route,
            [],
            page: $page,
            limit: 10,
            pages: ceil(count($items) / 10),
            absolute: true,
            total: count($items),
        );
    }
}
