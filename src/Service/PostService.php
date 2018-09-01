<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Comment;
use App\Entity\Post;
use App\Exception\FloodingException;
use App\Exception\InvalidEntityException;
use App\Exception\NotEnoughCoinsException;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostService
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var NotificationService */
    private $notificationService;
    /** @var ValidatorInterface */
    private $validator;
    /** @var AccountService */
    private $accountService;
    /** @var TransactionService */
    private $transactionService;
    /** @var PostRepository */
    private $postRepository;
    /** @var CommentService */
    private $commentService;

    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        NotificationService $notificationService,
        AccountService $accountService,
        TransactionService $transactionService,
        CommentService $commentService
    ) {
        $this->em = $em;
        $this->notificationService = $notificationService;
        $this->validator = $validator;
        $this->accountService = $accountService;
        $this->transactionService = $transactionService;
        $this->commentService = $commentService;
        $this->postRepository = $this->em->getRepository('App:Post');
    }

    public function findAll(): QueryBuilder
    {
        return $this->postRepository->findAllPaginated();
    }

    /**
     * @param Comment $comment
     * @throws NotEnoughCoinsException
     * @throws InvalidEntityException
     * @throws FloodingException
     */
    public function addComment(Comment $comment): void
    {
        $this->userCanComment($comment);

        $violations = $this->validator->validate($comment);

        if ($violations->count() > 0) {
            throw new InvalidEntityException($violations);
        }

        if ($comment->coins > 0) {
            $this->transactionService->debit($comment->author, $comment->coins);
        }

        $this->em->persist($comment);
        $this->em->flush();

        $this->notificationService->create(
            $comment->post->author,
            'Someone commented on your post',
            sprintf('The user %s has commented on your post "%s"', $comment->author->name, $comment->post->title)
        );
    }

    /**
     * @param Comment $comment
     * @return bool
     * @throws FloodingException when user comment before cooldown of their last comment
     * @throws \RuntimeException
     */
    private function userCanComment(Comment $comment): bool
    {
        $this->accountService->isFlooding($comment->author);

        if ($comment->coins === 0 && $comment->author->role === Account::ROLE_GUEST && $comment->post->author === Account::ROLE_GUEST) {
            throw new \RuntimeException('You can\'t comment on this post');
        }

        return true;
    }

    public function removeComment(Account $account, Comment $comment): void
    {
        if (($account->id !== $comment->author->id) && ($account->id !== $comment->post->author->id)) {
            throw new \RuntimeException('Somente o dono do comentário ou o dono do post pode apagar este comentário');
        }

        $this->em->remove($comment);
        $this->em->flush();
    }

    public function removeAllUserComments(Account $account, Post $post): void
    {
        $comments = $this->commentService->findAllCommentsByAccountAndPost($account, $post);

        foreach ($comments as $comment) {
            $this->em->remove($comment);
        }

        $this->em->flush();
    }
}