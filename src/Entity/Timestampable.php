<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Timestampable
 */
trait Timestampable
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    public $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    public $createdAt;

    /**
     * @ORM\PrePersist()
     */
    public function prePersistTimestampable(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdateTimestampable(): void
    {
        $this->updatedAt = new \DateTime();
    }

}