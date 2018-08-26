<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Account
 *
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Account
{
    use Timestampable;

    public const ROLE_SUBSCRIBER = 'subscriber';

    public const ROLE_GUEST = 'guest';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    public $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=12)
     */
    public $role;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    public $coins;

}