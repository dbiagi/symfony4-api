<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class CommentRepository extends EntityRepository
{
    public function findAllCommentsByAccountId(int $authorId): QueryBuilder
    {
        return $this->createQueryBuilder('comment')
            ->join('comment.author', 'author')
            ->where('author.id = :authorId')
            ->setParameter('authorId', $authorId);
    }

    public function findAllCommentsByPostId(int $postId): QueryBuilder
    {
        return $this->createQueryBuilder('comment')
            ->join('comment.post', 'post')
            ->where('post.id = :postId')
            ->setParameter('postId', $postId);
    }

    public function findLastCommentByAccountId(int $accountId): ?Comment
    {
        $qb = $this->createQueryBuilder('comment')
            ->join('comment.author', 'author')
            ->where('author.id = :accountId')
            ->setParameter('accountId', $accountId)
            ->orderBy('comment.createdAt', 'DESC');

        return $qb->getQuery()->getSingleResult();
    }
}