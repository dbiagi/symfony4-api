<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Comment;
use App\Entity\Post;
use App\Exception\InvalidEntityException;
use App\Exception\NotEnoughCoinsException;
use App\Helper\CommentSorter;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommentService
{
    /** @var CommentRepository */
    private $commentsRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TransactionService
     */
    private $transactionService;
    /**
     * @var NotificationService
     */
    private $notificationService;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, TransactionService $transactionService, NotificationService $notificationService)
    {
        $this->commentsRepository  = $em->getRepository('App:Comment');
        $this->em                  = $em;
        $this->transactionService  = $transactionService;
        $this->notificationService = $notificationService;
        $this->validator           = $validator;
    }

    /**
     * @param Comment $comment
     * @throws NotEnoughCoinsException
     * @throws InvalidEntityException
     */
    public function create(Comment $comment): void
    {
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

    public function getCommentsByAccount(Account $account): QueryBuilder
    {
        return $this->commentsRepository->findAllCommentsByAccountId($account->id);
    }

    public function getCommentsByPost(Post $post): array
    {
        $query = $this->commentsRepository->findAllCommentsByPostId($post->id);

        return CommentSorter::sort($query->getQuery()->getResult());
    }

    /**
     * @param Comment $comment
     */
    public function removeComment(Comment $comment): void
    {
        $this->em->remove($comment);
        $this->em->flush();
    }

    public function removeAllUserComments(Account $account, Post $post): void
    {
        $comments = $this->findAllCommentsByAccountAndPost($account, $post);

        foreach ($comments as $comment) {
            $this->em->remove($comment);
        }

        $this->em->flush();
    }

    public function findAllCommentsByAccountAndPost(Account $account, Post $post)
    {
        return $this->commentsRepository->findAllCommentsByAccountAndPost($account, $post);
    }
}