<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Exception\NotEnoughCoinsException;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class TransactionService
{
    /** @var TransactionRepository */
    private $transactionRepository;

    /** @var EntityManagerInterface */
    private $em;

    private $taxPercentage;

    public function __construct(EntityManagerInterface $em, $taxPercentage)
    {
        $this->em                    = $em;
        $this->taxPercentage         = $taxPercentage;
        $this->transactionRepository = $em->getRepository(Transaction::class);
    }

    /**
     * @param Account $account
     * @param int $coins
     * @return Transaction
     * @throws NotEnoughCoinsException
     */
    public function debit(Account $account, int $coins): Transaction
    {
        if ($account->coins < $coins) {
            throw new NotEnoughCoinsException($coins - $account->coins);
        }

        $t          = new Transaction();
        $t->account = $account;
        $t->total   = $coins;
        $t->type    = Transaction::TYPE_DEBT;

        $account->coins -= $coins;

        $this->em->persist($t);
        $this->em->persist($account);

        $this->tax($account, $t);

        $this->em->flush();

        return $t;
    }

    private function tax(Account $account, Transaction $reference): void
    {
        $taxTotal = $reference->total * ($this->taxPercentage / 100);

        $t            = new Transaction();
        $t->account   = $account;
        $t->total     = $taxTotal;
        $t->type      = Transaction::TYPE_DEBT;
        $t->reference = $reference;

        $reference->total -= $taxTotal;

        $this->em->persist($reference);
        $this->em->persist($t);
    }

    public function credit(Account $account, int $coins): Transaction
    {
        $t              = new Transaction();
        $t->account     = $account;
        $t->type        = Transaction::TYPE_CREDIT;
        $t->total       = $coins;
        $account->coins += $coins;

        $this->em->persist($t);
        $this->em->persist($account);

        $this->em->flush();

        return $t;
    }

    public function getTransactionByAccount(Account $account): QueryBuilder
    {
        return $this->transactionRepository->findTransctionsByAccountId($account->id);
    }
}