<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository
{
    public function findAllPaginated()
    {
        return $this->createQueryBuilder('account');
    }
}