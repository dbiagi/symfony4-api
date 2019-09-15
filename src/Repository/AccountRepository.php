<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;
use Doctrine\ORM\QueryBuilder;

class AccountRepository extends AppRepository
{
    public function findAllPaginated(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
                                   ->from(Account::class, 'account');
    }
}