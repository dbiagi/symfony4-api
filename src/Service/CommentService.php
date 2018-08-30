<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Post;
use App\Helper\CommentSorter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class CommentService
{
    /** @var \App\Repository\CommentRepository */
    private $commentsRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->commentsRepository = $em->getRepository('App:Comment');
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

    public function findAllCommentsByAccountAndPost(Account $account, Post $post)
    {
        return $this->commentsRepository->findAllCommentsByAccountAndPost($account, $post);
    }
}