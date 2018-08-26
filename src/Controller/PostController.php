<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Paginator\DoctrineQueryBuilderPaginator;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class PostController
 * @Route("/posts")
 */
class PostController extends AbstractController
{
    /** @var CommentService */
    private $commentService;
    /** @var DoctrineQueryBuilderPaginator */
    private $paginator;
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(CommentService $commentService, SerializerInterface $serializer, DoctrineQueryBuilderPaginator $paginator)
    {
        $this->commentService = $commentService;
        $this->serializer = $serializer;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/{id}/comments")
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function comments(Request $request, Post $post): Response
    {
        $query = $this->commentService->getCommentsByPost($post);

        $comments = $this->paginator->paginate($query, (int)$request->get('page', 1));

        $data = $this->serializer->serialize($comments, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

}