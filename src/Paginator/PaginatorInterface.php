<?php declare(strict_types=1);

namespace App\Paginator;

interface PaginatorInterface
{
    public function paginate($target, int $page = 1, int $itensPerPage = 10, array $context = []): Pagination;

    public function supports($class): bool;
}