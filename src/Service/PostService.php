<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Comment;
use App\Exception\FloodingException;
use App\Exception\InvalidEntityException;
use App\Exception\NotEnoughCoinsException;
use App\Mailer\CommentNotificationMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostService
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var CommentNotificationMailer */
    private $mailer;
    /** @var ValidatorInterface */
    private $validator;
    /** @var AccountService */
    private $accountService;

    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        CommentNotificationMailer $mailer,
        AccountService $accountService
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->validator = $validator;
        $this->accountService = $accountService;
    }

    /**
     * @param Comment $comment
     * @throws NotEnoughCoinsException
     * @throws InvalidEntityException
     * @throws FloodingException
     */
    public function addComment(Comment $comment)
    {
        $this->userCanComment($comment);

        $violations = $this->validator->validate($comment);

        if ($violations->count() > 0) {
            throw new InvalidEntityException($violations);
        }

        if ($comment->coins > 0) {
            // @TODO gravar a transação aqui
        }

        $this->em->persist($comment);
        $this->em->flush();

        $this->mailer->send($comment);
    }

    /**
     * @param Comment $comment
     * @return bool
     * @throws FloodingException when user comment before cooldown of their last comment
     * @throws \RuntimeException
     * @throws NotEnoughCoinsException when user hasn't enough coins
     */
    private function userCanComment(Comment $comment): bool
    {
        $this->accountService->isFlooding($comment->author);

        $this->hasEnoughCoins($comment);

        if ($comment->coins === 0 && $comment->author->role === Account::ROLE_GUEST && $comment->post->author === Account::ROLE_GUEST) {
            throw new \RuntimeException('You can\'t comment on this post');
        }

        return true;
    }

    /**
     * @param Comment $comment
     * @return bool
     * @throws NotEnoughCoinsException
     */
    private function hasEnoughCoins(Comment $comment)
    {
        if ($comment->coins > 0 && $comment->author->coins < $comment->coins) {
            throw new NotEnoughCoinsException($comment->coins - $comment->author->coins);
        }

        return true;
    }
}