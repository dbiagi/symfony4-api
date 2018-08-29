<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Paginator\Paginator;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class NotificationController
 * @Route("/notifications")
 */
class NotificationController extends AbstractController
{
    /** @var Paginator */
    private $paginator;
    /** @var SerializerInterface */
    private $serializer;
    /** @var NotificationService */
    private $notificationService;

    public function __construct(
        NotificationService $notificationService,
        SerializerInterface $serializer,
        Paginator $paginator
    ) {
        $this->notificationService = $notificationService;
        $this->serializer = $serializer;
        $this->paginator = $paginator;
    }

    public function listByAccount(Account $account)
    {
        $notifications = $this->notificationService->getNotificationByAccount($account);
    }
}