<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Notification;
use App\Exception\InvalidEntityException;
use App\Mailer\NotificationMailer;
use App\Repository\NotificationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationService
{
    /** @var NotificationRepository */
    private $notificationRepository;

    /** @var ValidatorInterface */
    private $validator;

    /** @var EntityManagerInterface */
    private $em;

    /** @var NotificationMailer */
    private $mailer;

    /** @var string */
    private $notificationLifespan;

    public function __construct(
        EntityManagerInterface $em,
        NotificationMailer $mailer,
        ValidatorInterface $validator,
        $notificationLifespan
    )
    {
        $this->em                     = $em;
        $this->mailer                 = $mailer;
        $this->notificationRepository = $em->getRepository('App:Notification');
        $this->validator              = $validator;
        $this->notificationLifespan   = $notificationLifespan;
    }

    public function getNotificationByAccount(Account $account): QueryBuilder
    {
        return $this->notificationRepository->findNotificationsByAccountId($account->id);
    }

    public function read(Notification $notification): void
    {
        if ($notification->viewedAt !== null) {
            return;
        }

        $notification->viewedAt = new DateTime();
        $notification->expireAt = (new DateTime())->modify(sprintf('+%d seconds', $this->notificationLifespan));

        $this->em->persist($notification);
        $this->em->flush();
    }

    /**
     * @param Account $to
     * @param $title
     * @param $content
     * @throws InvalidEntityException
     * @throws Exception
     */
    public function create(Account $to, $title, $content): void
    {
        $n = new Notification();

        $n->account = $to;
        $n->title   = $title;
        $n->content = $content;

        $violations = $this->validator->validate($n);

        if ($violations->count() > 0) {
            throw new InvalidEntityException($violations);
        }

        $this->em->persist($n);
        $this->em->flush();

        $this->mailer->send($n);
    }
}