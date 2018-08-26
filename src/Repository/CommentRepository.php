<?php declare(strict_types=1);

namespace App\Repository;

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
}