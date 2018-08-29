<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Comment;
use App\Entity\Post;
use App\Exception\InvalidEntityException;
use App\Paginator\DoctrineQueryBuilderPaginator;
use App\Service\AccountService;
use App\Service\CommentService;
use App\Service\PostService;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @Route("/{id}", methods={"get"})
     *
     * @param Post $post
     * @return JsonResponse
     */
    public function getAction(Post $post)
    {
        $data = $this->serializer->serialize($post, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/", methods={"get"})
     *
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function listAction(Request $request)
    {
        $query = $this->postService->findAll();

        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        $data = $this->serializer->serialize($pagination, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}/comments", methods={"get"})
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function comments(Request $request, Post $post): Response
    {
        $query = $this->commentService->getCommentsByPost($post);

        $comments = $this->paginator->paginate($query, $request->query->getInt('page', 1));

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

        return new JsonResponse(
            $this->serializer->serialize($comment, 'json'),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    /**
     * @Route("/{post_id}/comments/{comment_id}", methods={"delete"})
     * @ParamConverter("post", options={"id" = "post_id"})
     * @ParamConverter("comment", options={"id" = "comment_id"})
     * @param Request $request
     * @param Post $post
     * @param Comment $comment
     * @return JsonResponse
     */
    public function deleteComment(Request $request, Post $post, Comment $comment)
    {
        $params = json_decode($request->getContent(), true);

        if (!$params['account']) {
            return new JsonResponse(
                ['error' => sprintf('Sua conta de usuário deve ser informada.')],
                Response::HTTP_BAD_REQUEST
            );
        }

        $account = $this->accountService->findByEmail($params['account']);

        if (!$account) {
            return new JsonResponse(
                ['error' => sprintf('Conta com email %s não encontrada.', $params['account'])],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->postService->removeComment($account, $comment);

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @Route("/{post_id}/{account_id}", methods={"delete"})
     * @ParamConverter("post", options={"id" = "post_id"})
     * @ParamConverter("account", options={"id" = "account_id"})
     * @param Request $request
     * @param Post $post
     * @param Account $account
     * @return JsonResponse
     */
    public function deleteCommentsOfUser(Request $request, Post $post, Account $account)
    {
        $params = json_decode($request->getContent(), true);

        if (!$params['account']) {
            return new JsonResponse(
                ['error' => sprintf('Sua conta de usuário deve ser informada.')],
                Response::HTTP_BAD_REQUEST
            );
        }

        $postAuthor = $this->accountService->findByEmail($params['account']);

        if (!$postAuthor) {
            return new JsonResponse(
                ['error' => sprintf('Conta com email %s não encontrada.', $params['account'])],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($postAuthor->id !== $post->author->id) {
            return new JsonResponse(
                ['error' => 'Somente o dono do post pode remover os comentários.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->postService->removeAllUserComments($account, $post);

        return new JsonResponse(null, Response::HTTP_OK);
    }
}