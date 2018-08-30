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
            new ArrayPaginator()
        ];
    }

    public function paginate($target, int $page = 1, int $itensPerPage = 10, array $context = [])
    {
        foreach ($this->paginators as $paginator) {
            if ((is_object($target) && $paginator->supports(get_class($target))) ||
                (is_array($target) && $paginator->supports([]))) {
                return $paginator->paginate($target, $page, $itensPerPage, $context);
            }
        }

        throw new \RuntimeException(
            sprintf('No paginator found for %s class',is_array($target) ? 'array' : get_class($target))
        );
    }
}