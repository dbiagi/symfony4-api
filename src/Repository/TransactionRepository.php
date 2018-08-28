<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class TransactionRepository extends EntityRepository
{
    public function findTransctionsByAccountId(int $accountId): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->join('t.account', 'account')
            ->where('account.id = :accountId')
            ->setParameter('accountId', $accountId);
    }
}