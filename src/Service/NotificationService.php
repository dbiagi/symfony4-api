<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class NotificationService
{
    /** @var \App\Repository\NotificationRepository */
    private $notificationRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->notificationRepository = $em->getRepository('App:Notification');
    }

    public function getNotificationByAccount(Account $account): QueryBuilder
    {
        return $this->notificationRepository->findNotificationsByAccountId($account->id);
    }
}