<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class NotificationRepository extends EntityRepository
{
    public function findNotificationsByAccountId(int $accountId): QueryBuilder
    {
        $qb = $this->createQueryBuilder('notification');

        return $qb->join('notification.account', 'account')
            ->andWhere('account.id = :accountId')
            ->setParameter('accountId', $accountId)
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('notification.expireAt'),
                $qb->expr()->gte('notification.expireAt', ':now')
            ))
            ->setParameter('now', date('Y-m-d G:i:s'));
    }
}