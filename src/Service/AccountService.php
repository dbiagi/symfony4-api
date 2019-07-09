<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Exception\InvalidEntityException;
use App\Repository\AccountRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AccountService
{
    /** @var AccountRepository */
    private $repository;
    /** @var CommentRepository */
    private $commentsRepository;
    /** @var ValidatorInterface */
    private $validator;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator
    )
    {
        $this->em                 = $em;
        $this->repository         = $em->getRepository('App:Account');
        $this->commentsRepository = $em->getRepository('App:Comment');
        $this->validator          = $validator;
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
     * @return Account
     * @throws InvalidEntityException
     */
    public function create(Account $account): Account
    {
        $violations = $this->validator->validate($account);

        if ($violations->count() > 0) {
            throw new InvalidEntityException($violations);
        }

        $this->em->persist($account);
        $this->em->flush();

        return $account;
    }
}