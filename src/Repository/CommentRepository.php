<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Comment;
use App\Entity\Post;
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
        $qb = $this->createQueryBuilder('comment');

        return $qb->join('comment.post', 'post')
                  ->where('post.id = :postId')
                  ->setParameter('postId', $postId)
                  ->addOrderBy('DATE_ADD(comment.createdAt, comment.coins, \'MINUTE\')', 'desc')
                  ->setMaxResults(100);
    }

    public function findLastCommentByAccountId(int $accountId): ?Comment
    {
        $qb = $this->createQueryBuilder('comment')
                   ->join('comment.author', 'author')
                   ->where('author.id = :accountId')
                   ->setParameter('accountId', $accountId)
                   ->setMaxResults(1)
                   ->orderBy('comment.createdAt', 'DESC');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAllCommentsByAccountAndPost(Account $account, Post $post): ?array
    {
        return $this->createQueryBuilder('comment')
                    ->join('comment.post', 'post')
                    ->join('comment.author', 'account')
                    ->andWhere('account.id = :accountId')
                    ->andWhere('post.id = :postId')
                    ->setParameter('accountId', $account->id)
                    ->setParameter('postId', $post->id)
                    ->getQuery()
                    ->getResult();
    }
}