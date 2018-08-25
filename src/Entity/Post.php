<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
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
     */
    public $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    public $content;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     */
    public $type;
}