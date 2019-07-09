<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Paginator\Paginator;
use App\Service\AccountService;
use App\Service\CommentService;
use App\Service\PostService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @Route("/posts")
 */
class PostController extends AbstractController
{
    /** @var CommentService */
    private $commentService;
    /** @var Paginator */
    private $paginator;
    /** @var SerializerInterface */
    private $serializer;
    /** @var AccountService */
    private $accountService;
    /** @var PostService */
    private $postService;

    public function __construct(
        CommentService $commentService,
        SerializerInterface $serializer,
        Paginator $paginator,
        AccountService $accountService,
        PostService $postService
    )
    {
        $this->commentService = $commentService;
        $this->serializer     = $serializer;
        $this->paginator      = $paginator;
        $this->accountService = $accountService;
        $this->postService    = $postService;
    }

    /**
     * @Route("/{id}", methods={"get"})
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function getAction(Post $post): JsonResponse
    {
        $data = $this->serializer->serialize($post, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/", methods={"get"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $query = $this->postService->findAll();

        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        $data = $this->serializer->serialize($pagination, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}