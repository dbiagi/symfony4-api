<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Paginator\Paginator;
use App\Service\AccountService;
use App\Service\NotificationService;
use App\Service\TransactionService;
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

    /** @var TransactionService */
    private $transactionService;

    public function __construct(
        AccountService $accountService,
        NotificationService $notificationService,
        TransactionService $transactionService,
        Paginator $paginator,
        SerializerInterface $serializer
    ) {
        $this->accountService = $accountService;
        $this->notificationService = $notificationService;
        $this->transactionService = $transactionService;
        $this->paginator = $paginator;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", methods={"get"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listAll(Request $request)
    {
        $query = $this->accountService->findAll();

        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        $data = $this->serializer->serialize($pagination, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/", methods={"post"})
     */
    public function post(): Response
    {

        return new Response('ok');
    }

    /**
     * @Route("/{id}", methods={"get"})
     *
     * @param Account $account
     * @return Response
     */
    public function getAccount(Account $account): Response
    {
        $data = $this->serializer->serialize($account, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
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

        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        $data = $this->serializer->serialize($pagination, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}/notifications")
     *
     * @param Request $request
     * @param Account $account
     * @return Response
     */
    public function notifications(Request $request, Account $account): Response
    {
        $query = $this->notificationService->getNotificationByAccount($account);

        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        $data = $this->serializer->serialize($pagination, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}/coins", methods={"post"})
     * @param Request $request
     * @param Account $account
     * @return JsonResponse
     */
    public function buyCoins(Request $request, Account $account)
    {
        $content = json_decode($request->getContent(), true);

        if(!isset($content['amount'])) {
            return new JsonResponse(['error' => 'Tem que ser informato o campo amount'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->transactionService->create($account, $content['amount'], Transaction::TYPE_CREDIT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $data = $this->serializer->serialize($account, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}