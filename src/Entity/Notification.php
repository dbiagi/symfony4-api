<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Notification
 *
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Notification
{
    use Timestampable;

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
     * @Assert\NotBlank()
     */
    public $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     */
    public $content;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     *
     * @Assert\NotNull()
     */
    public $account;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    public $viewedAt;
}