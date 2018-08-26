<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Paginator\Paginator;
use App\Service\AccountService;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AccountController
 * @Route("/accounts")
 */
class AccountController extends AbstractController
{
    /** @var AccountService */
    private $accountService;

    /** @var Paginator */
    private $paginator;

    /** @var SerializerInterface */
    private $serializer;

    /** @var NotificationService */
    private $notificationService;

    public function __construct(
        AccountService $accountService,
        NotificationService $notificationService,
        Paginator $paginator,
        SerializerInterface $serializer
    ) {
        $this->accountService = $accountService;
        $this->notificationService = $notificationService;
        $this->paginator = $paginator;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", methods={"post"})
     */
    public function post(): Response
    {

        return new Response('ok');
    }

    /**
     * @Route("/{id}/comments")
     *
     * @param Request $request
     * @param Account $account
     *
     * @return Response
     */
    public function comments(Request $request, Account $account): Response
    {
        $query = $this->accountService->getComments($account->id);

        $pagination = $this->paginator->paginate($query, (int)$request->get('page', 1));

        $data = $this->serializer->serialize($pagination, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    public function notifications(Request $request, Account $account): Response
    {
        $this->notificationService->getNotificationByAccount($account);
    }
}