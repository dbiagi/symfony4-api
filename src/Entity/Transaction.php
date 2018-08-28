<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Transaction
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Transaction
{
    use Timestampable;

    /** @var string Representa um débito em relação a Account */
    public const TYPE_DEBT = 'debt';
    /** @var string Representa um crédito em relação a Account */
    public const TYPE_CREDIT = 'credit';

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
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    public $total;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=12)
     * @Assert\NotBlank()
     */
    public $type;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    public $account;

    /**
     * @var Transaction
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Transaction")
     * @ORM\JoinColumn(nullable=true)
     */
    public $reference;
}