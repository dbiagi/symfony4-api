<?php declare(strict_types=1);

namespace App\Paginator;

use Doctrine\ORM\QueryBuilder;

class DoctrineQueryBuilderPaginator implements PaginatorInterface
{
    /**
     * @param QueryBuilder $qb
     * @param int $page
     * @param int $itensPerPage
     * @param array $context
     * @return Pagination
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function paginate($qb, int $page = 1, int $itensPerPage = 10, array $context = []): Pagination
    {
        $qb->select(sprintf('COUNT(%d)', $qb->getRootAlias()));
        $count = $qb->getQuery()->getSingleScalarResult();

        $qb->select($qb->getRootAliases());

        $data = $qb->setMaxResults($itensPerPage)
            ->setFirstResult(($page - 1) * $itensPerPage)
            ->getQuery()
            ->getResult();

        return new Pagination($data, (int)$count);
    }

    public function supports($target): bool
    {
        return $target === QueryBuilder::class;
    }
}