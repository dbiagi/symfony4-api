<?php

namespace App\Controller;

use App\Comment\CanCommentChecker;
use App\Entity\Account;
use App\Entity\Comment;
use App\Entity\Post;
use App\Exception\FloodingException;
use App\Exception\InvalidEntityException;
use App\Exception\NotEnoughCoinsException;
use App\Paginator\Paginator;
use App\Response\BadRequestJsonResponse;
use App\Service\AccountService;
use App\Service\CommentService;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{

    /**
     * @var CommentService
     */
    private $commentService;
    /**
     * @var AccountService
     */
    private $accountService;
    /**
     * @var Paginator
     */
    private $paginator;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var CanCommentChecker
     */
    private $canCommentChecker;

    public function __construct(CommentService $commentService, AccountService $accountService, CanCommentChecker $canCommentChecker, Paginator $paginator, SerializerInterface $serializer)
    {
        $this->commentService    = $commentService;
        $this->accountService    = $accountService;
        $this->canCommentChecker = $canCommentChecker;
        $this->paginator         = $paginator;
        $this->serializer        = $serializer;
    }

    /**
     * @Route("/{id}/comments", methods={"get"})
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function list(Request $request, Post $post): Response
    {
        $results = $this->commentService->getCommentsByPost($post);

        $comments = $this->paginator->paginate($results, $request->query->getInt('page', 1));

        $data = $this->serializer->serialize($comments, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}/comments", methods={"post"})
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse|Response
     */
    public function create(Request $request, Post $post)
    {
        $params = json_decode($request->getContent(), true);

        $account = $this->accountService->findByEmail($params['account']);

        if (!$account) {
            return new JsonResponse(['error' => 'Account not found'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Comment $comment */
        $comment = $this->serializer->deserialize($request->getContent(), Comment::class, 'json');

        $comment->post   = $post;
        $comment->author = $account;

        try {
            $this->canCommentChecker->check($comment);
        } catch (FloodingException $e) {
            return new BadRequestJsonResponse($e->getMessage());
        }

        try {
            $this->commentService->create($comment);
        } catch (InvalidEntityException $e) {
            return new BadRequestJsonResponse($e->getMessage(), $e->getErrors());
        } catch (NotEnoughCoinsException $e) {
            return new BadRequestJsonResponse($e->getMessage());
        }

        return new JsonResponse($this->serializer->serialize($comment, 'json'), Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/comments/{id}", methods={"delete"})
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function delete(Request $request, Comment $comment): JsonResponse
    {
        $params = json_decode($request->getContent(), true);

        if (!$params['account']) {
            return new BadRequestJsonResponse('Sua conta de usuário deve ser informada.');
        }

        $account = $this->accountService->findByEmail($params['account']);

        if (!$account) {
            return new BadRequestJsonResponse(sprintf('Conta com email %s não encontrada.', $params['account']));
        }

        if (($account->uuid !== $comment->author->uuid) && ($account->uuid !== $comment->post->author->uuid)) {
            return new BadRequestJsonResponse('Somente o dono do comentário ou o dono do post pode apagar este comentário');
        }

        $this->commentService->removeComment($comment);

        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @Route("/{post_id}/comments/accounts/{account_id}", methods={"delete"})
     * @ParamConverter("post", options={"id" = "post_id"})
     * @ParamConverter("account", options={"id" = "account_id"})
     * @param Request $request
     * @param Post $post
     * @param Account $account
     * @return JsonResponse
     */
    public function deleteCommentsOfAnUser(Request $request, Post $post, Account $account): JsonResponse
    {
        $params = json_decode($request->getContent(), true);

        if (!$params['account']) {
            return new BadRequestJsonResponse('Sua conta de usuário deve ser informada.');
        }

        $postAuthor = $this->accountService->findByEmail($params['account']);

        if (!$postAuthor) {
            return new BadRequestJsonResponse(sprintf('Conta com email %s não encontrada.', $params['account']));
        }

        if ($postAuthor->uuid !== $post->author->uuid) {
            new BadRequestJsonResponse('Somente o dono do post pode remover os comentários.');
        }

        $this->commentService->removeAllUserComments($account, $post);

        return new JsonResponse(null, Response::HTTP_OK);
    }
}