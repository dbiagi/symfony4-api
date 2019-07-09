<?php declare(strict_types=1);

namespace App\Paginator;

interface PaginatorInterface
{
    public function paginate($target, $page = 1, $itensPerPage = 10, array $context = []): Pagination;

    public function supports($target): bool;
}