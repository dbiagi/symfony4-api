<?php declare(strict_types=1);

namespace App\Paginator;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

class DoctrineQueryBuilderPaginator implements PaginatorInterface
{
    /**
     * @param QueryBuilder $qb
     * @param int $page
     * @param int $itensPerPage
     * @param array $context
     * @return Pagination
     * @throws NonUniqueResultException
     */
    public function paginate($qb, $page = 1, $itensPerPage = 10, array $context = []): Pagination
    {
        $qb->select(sprintf('COUNT(%d)', $qb->getRootAliases()));
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
        return is_object($target) && get_class($target) === QueryBuilder::class;
    }
}
