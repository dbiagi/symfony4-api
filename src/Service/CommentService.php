<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Comment;
use App\Entity\Post;
use App\Event\CommentEventArgs;
use App\Event\Type\Comment as CommentEventType;
use App\Helper\CommentSorter;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CommentService
{
    /** @var CommentRepository */
    private $commentsRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->commentsRepository = $em->getRepository('App:Comment');
        $this->em                 = $em;
        $this->eventDispatcher    = $eventDispatcher;
    }

    /**
     * @param Comment $comment
     */
    public function create(Comment $comment): void
    {
        $args = new CommentEventArgs();
        $args->setComment($comment);

        $this->eventDispatcher->dispatch(CommentEventType::COMMENT_CREATED, $args);

        $this->em->persist($comment);
        $this->em->flush();

        $this->eventDispatcher->dispatch(CommentEventType::COMMENT_PERSISTED, $args);
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