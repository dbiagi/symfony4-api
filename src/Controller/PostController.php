<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Exception\InvalidEntityException;
use App\Paginator\DoctrineQueryBuilderPaginator;
use App\Service\AccountService;
use App\Service\CommentService;
use App\Service\PostService;
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
    /** @var AccountService */
    private $accountService;
    /** @var PostService */
    private $postService;

    public function __construct(
        CommentService $commentService,
        SerializerInterface $serializer,
        DoctrineQueryBuilderPaginator $paginator,
        AccountService $accountService,
        PostService $postService
    ) {
        $this->commentService = $commentService;
        $this->serializer = $serializer;
        $this->paginator = $paginator;
        $this->accountService = $accountService;
        $this->postService = $postService;
    }

    /**
     * @Route("/{id}/comments", methods={"get"})
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public
    function comments(
        Request $request,
        Post $post
    ): Response {
        $query = $this->commentService->getCommentsByPost($post);

        $comments = $this->paginator->paginate($query, $request->attributes->getInt('page', 1));

        $data = $this->serializer->serialize($comments, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}/comments", methods={"post"})
     *
     * @param Post $post
     * @return JsonResponse|Response
     */
    public function comment(Request $request, Post $post)
    {
        $params = json_decode($request->getContent(), true);

        $account = $this->accountService->findByEmail($params['account']);

        if (!$account) {
            return new JsonResponse(['error' => 'Account not found'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Comment $comment */
        $comment = $this->serializer->deserialize($request->getContent(), Comment::class, 'json');

        $comment->post = $post;
        $comment->author = $account;

        try {
            $this->postService->addComment($comment);
        } catch (InvalidEntityException $e) {
            return new JsonResponse(['errors' => $e->getErrors()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}