<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class Transaction
{
    use Timestampable;

    public const TRANSACTION_TYPE_COIN = 'coin';
    public const TRANSACTION_TYPE_SUBSCRIPTION = 'subscription';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @var double
     *
     * @ORM\Column(type="decimal", scale=12, precision=2)
     */
    public $total;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=12)
     */
    public $type;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    public $account;
}