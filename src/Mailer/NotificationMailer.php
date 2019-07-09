<?php declare(strict_types=1);

namespace App\Mailer;

use App\Entity\Notification;
use Swift_Mailer;
use Swift_Message;

class NotificationMailer
{
    /** @var Swift_Mailer */
    private $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Notification $notification): void
    {
        $message = (new Swift_Message())
            ->addTo($notification->account->email)
            ->setSubject($notification->title)
            ->setBody($notification->content);

        $this->mailer->send($message);
    }
}