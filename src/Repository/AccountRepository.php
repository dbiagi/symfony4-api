<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AccountRepository extends EntityRepository
{
    public function findAllPaginated(): QueryBuilder
    {
        return $this->createQueryBuilder('account');
    }
}