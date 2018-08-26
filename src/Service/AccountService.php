<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\AccountRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class AccountService
{
    /** @var AccountRepository */
    private $repository;
    /** @var CommentRepository */
    private $commentsRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository('App:Account');
        $this->commentsRepository = $em->getRepository('App:Comment');
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function getComments(int $accountId): QueryBuilder
    {
        return $this->commentsRepository->findAllCommentsByAccountId($accountId);
    }
}