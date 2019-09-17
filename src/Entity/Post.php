<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    use Timestampable;

    public const TYPE_PHOTO = 'photo';

    public const TYPE_VIDEO = 'video';

    public const TYPE_TEXT = 'text';

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
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    public $content;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getPostTypes")
     */
    public $type;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account")
     * @ORM\JoinColumn(name="author_uuid", referencedColumnName="uuid")
     * @Assert\NotNull()
     */
    public $author;

    public static function getPostTypes(): array
    {
        return [
            self::TYPE_PHOTO,
            self::TYPE_TEXT,
            self::TYPE_VIDEO,
        ];
    }
}