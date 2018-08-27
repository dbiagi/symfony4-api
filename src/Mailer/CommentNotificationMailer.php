<?php declare(strict_types=1);

namespace App\Mailer;

use App\Entity\Comment;

class CommentNotificationMailer
{
    /** @var \Swift_Mailer */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(Comment $comment)
    {
        $message = (new \Swift_Message())
            ->addTo($comment->author->email)
            ->setSubject('Someone commented on your post')
            ->setBody(sprintf(
                    'The user %s has commented on your post named "%s"',
                    $comment->author->name,
                    $comment->post->title
                )
            );

        $this->mailer->send($message);
    }
}