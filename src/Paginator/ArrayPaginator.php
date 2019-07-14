<?php declare(strict_types=1);

namespace App\Paginator;

class ArrayPaginator implements PaginatorInterface
{
    public function paginate($target, $page = 1, $itensPerPage = 10, array $context = []): Pagination
    {
        $data = array_slice($target, ($page - 1) * $itensPerPage, $itensPerPage);

        return new Pagination($data, count($target));
    }

    public function supports($target): bool
    {
        return is_array($target);
    }
}