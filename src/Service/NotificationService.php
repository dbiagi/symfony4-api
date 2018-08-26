<?php declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    /** @var \App\Repository\NotificationRepository */
    private $notificationRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->notificationRepository = $em->getRepository('App:Notification');
    }

    public function getNotificationByAccount(\App\Entity\Account $account)
    {
    }
}