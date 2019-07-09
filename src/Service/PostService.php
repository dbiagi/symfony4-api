<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class PostService
{
    /** @var PostRepository */
    private $postRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->postRepository = $em->getRepository('App:Post');
    }

    public function findAll(): QueryBuilder
    {
        return $this->postRepository->findAllPaginated();
    }
}