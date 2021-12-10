<?php

namespace App\Service;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

class PaginationService
{
    public function paginate(int $count, mixed $data, string $route, int $page): PaginatedRepresentation
    {
        return new PaginatedRepresentation(
            new CollectionRepresentation($data),
            $route,
            [],
            page: $page,
            limit: 5,
            pages: ceil($count / 5),
            absolute: true,
            total: $count,
        );
    }
}
