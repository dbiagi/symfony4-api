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

    public function paginate($target, int $page = 1, int $itensPerPage = 10, array $context = [])
    {
        foreach ($this->paginators as $paginator) {
            if($paginator->supports(get_class($target))) {
                return $paginator->paginate($target, $page, $itensPerPage, $context);
            }
        }

        throw new \RuntimeException(sprintf('No paginator found for %s class', get_class($target)));
    }

    private function load(): void
    {
        $this->paginators = [
            new DoctrineQueryBuilderPaginator()
        ];
    }
}