<?php

namespace App\EventListeners;

use App\Event\CommentEventArgs;
use App\Event\Type\Comment;
use App\Exception\InvalidEntityException;
use App\Exception\NotEnoughCoinsException;
use App\Service\NotificationService;
use App\Service\TransactionService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentCreated implements EventSubscriberInterface
{
    /**
     * @var NotificationService
     */
    private $notificationService;
    /**
     * @var TransactionService
     */
    private $transactionService;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(NotificationService $notificationService, TransactionService $transactionService, ValidatorInterface $validator)
    {
        $this->notificationService = $notificationService;
        $this->transactionService  = $transactionService;
        $this->validator = $validator;
    }

    /**
     * @return array|void
     */
    public static function getSubscribedEvents()
    {
        return [
            Comment::COMMENT_CREATED   => 'onCommentCreated',
            Comment::COMMENT_PERSISTED => 'onCommentPersisted'
        ];
    }

    /**
     * @param CommentEventArgs $args
     * @throws InvalidEntityException
     * @throws NotEnoughCoinsException
     */
    public function onCommentPersisted(CommentEventArgs $args)
    {
        $comment = $args->getComment();

        if ($comment->coins > 0) {
            $this->transactionService->debit($comment->author, $comment->coins);
        }

        $this->notificationService->create(
            $comment->post->author,
            'Someone commented on your post',
            sprintf('The user %s has commented on your post "%s"', $comment->author->name, $comment->post->title)
        );
    }

    /**
     * @param CommentEventArgs $args
     * @throws InvalidEntityException
     */
    public function onCommentCreated(CommentEventArgs $args)
    {
        $comment    = $args->getComment();
        $violations = $this->validator->validate($comment);

        if ($violations->count() > 0) {
            throw new InvalidEntityException($violations);
        }
    }
}