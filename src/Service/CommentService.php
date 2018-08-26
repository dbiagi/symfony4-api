<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Post;
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

    /**
     * @param Account $account
     * @return QueryBuilder
     */
    public function getCommentsByAccount(Account $account): QueryBuilder
    {
        return $this->commentsRepository->findAllCommentsByAccountId($account->id);
    }

    /**
     * @param Post $post
     * @return QueryBuilder
     */
    public function getCommentsByPost(Post $post): QueryBuilder
    {
        return $this->commentsRepository->findAllCommentsByPostId($post->id);
    }
}