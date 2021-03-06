<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Account
 *
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 */
class Account
{
    use Timestampable;

    public const ROLE_SUBSCRIBER = 'subscriber';

    public const ROLE_GUEST = 'guest';

    /**
     * @ORM\Id
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid")
     * @JMS\Exclude()
     */
    public $uuid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    public $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=12)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getAccountRoles")
     */
    public $role;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    public $coins = 0;

    public static function getAccountRoles(): array
    {
        return [self::ROLE_GUEST, self::ROLE_SUBSCRIBER];
    }
}