<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Comment
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
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    public $coin;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    public $content;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Account")
     */
    public $author;

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Post")
     */
    public $post;
}