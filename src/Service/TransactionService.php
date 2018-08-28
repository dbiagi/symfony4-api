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
        $this->em = $em;
        $this->taxPercentage = $taxPercentage;
        $this->transactionRepository = $em->getRepository('App:Transaction');
    }

    /**
     * @param Account $account
     * @param int $coins
     * @param $type
     * @return Transaction
     * @throws NotEnoughCoinsException
     */
    public function create(Account $account, int $coins, $type): Transaction
    {
        if (!in_array($type, [Transaction::TYPE_DEBT, Transaction::TYPE_CREDIT])) {
            throw new \RuntimeException(sprintf('Transction of type %s not supported', $type));
        }

        if ($type === Transaction::TYPE_DEBT && $account->coins < $coins) {
            throw new NotEnoughCoinsException($coins - $account->coins);
        }

        $t = new Transaction();
        $t->account = $account;
        $t->total = $coins;
        $t->type = $type;

        $account->coins += $type === Transaction::TYPE_CREDIT ? $coins : -$coins;

        $this->em->persist($t);
        $this->em->persist($account);

        $this->tax($account, $t);

        $this->em->flush();

        return $t;
    }

    public function tax(Account $account, Transaction $reference, $flush = false)
    {
        $taxTotal = $reference->total * ($this->taxPercentage / 100);

        $t = new Transaction();
        $t->account = $account;
        $t->total = $taxTotal;
        $t->type = Transaction::TYPE_DEBT;
        $t->reference = $reference;

        $reference->total -= $taxTotal;

        $this->em->persist($reference);
        $this->em->persist($t);

        if ($flush) {
            $this->em->flush();
        }
    }

    public function getTransactionByAccount(Account $account): QueryBuilder
    {
        return $this->transactionRepository->findTransctionsByAccountId($account->id);
    }
}