<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Exception\FloodingException;
use App\Helper\DateTimeHelper;
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
    /** @var string */
    private $commentCooldown;

    public function __construct(EntityManagerInterface $em, TransactionService $transactionService, $commentCooldown)
    {
        $this->repository = $em->getRepository('App:Account');
        $this->commentsRepository = $em->getRepository('App:Comment');
        $this->transactionService = $transactionService;
        $this->commentCooldown = $commentCooldown;
    }

    public function find(int $id): ?Account
    {
        return $this->repository->find($id);
    }

    public function findAll(): QueryBuilder
    {
        return $this->repository->findAllPaginated();
    }

    public function getComments(int $accountId): QueryBuilder
    {
        return $this->commentsRepository->findAllCommentsByAccountId($accountId);
    }

    public function findByEmail($email): ?Account
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    /**
     * @param Account $account
     * @return bool
     * @throws FloodingException
     */
    public function isFlooding(Account $account): bool
    {
        $comment = $this->commentsRepository->findLastCommentByAccountId($account->id);

        if ($comment === null) {
            return false;
        }

        $now = new \DateTime();

        $secondsAfterLastComment = DateTimeHelper::getDiffInSeconds($comment->createdAt, $now);

        if ($secondsAfterLastComment < $this->commentCooldown) {
            throw new FloodingException($this->commentCooldown - $secondsAfterLastComment);
        }

        return false;
    }
}