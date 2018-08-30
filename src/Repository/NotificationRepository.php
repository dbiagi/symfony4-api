<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class NotificationRepository extends EntityRepository
{
    public function findNotificationsByAccountId(int $accountId): QueryBuilder
    {
        return $this->createQueryBuilder('notification')
            ->join('notification.account', 'account')
            ->andWhere('account.id = :accountId')
            ->setParameter('accountId', $accountId)
            ->andWhere('notification.expireAt >= :now')
            ->setParameter('now', date('Y-m-d G:i:s'));
    }
}