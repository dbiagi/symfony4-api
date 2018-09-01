<?php declare(strict_types=1);

namespace App\Paginator;

class Paginator
{
    /** @var PaginatorInterface[] */
    private $paginators;

    public function __construct()
    {
        $this->load();
    }

    private function load(): void
    {
        $this->paginators = [
            new DoctrineQueryBuilderPaginator(),
            new ArrayPaginator(),
        ];
    }

    public function paginate($target, int $page = 1, int $itensPerPage = 10, array $context = []): Pagination
    {
        foreach ($this->paginators as $paginator) {
            if ($paginator->supports($target)) {
                return $paginator->paginate($target, $page, $itensPerPage, $context);
            }
        }

        $type = is_object($target) ? get_class($target) : gettype($target);

        throw new \RuntimeException(sprintf('No paginator found for %s class', $type));
    }
}
