<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Notification;
use App\Exception\InvalidEntityException;
use App\Paginator\Paginator;
use App\Service\AccountService;
use App\Service\NotificationService;
use App\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AccountController
 * @Route("/accounts")
 */
class AccountController extends AbstractController
{
    /** @var Paginator */
    private $paginator;

    /** @var SerializerInterface */
    private $serializer;

    /** @var NotificationService */
    private $notificationService;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        NotificationService $notificationService,
        Paginator $paginator,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager
    )
    {
        $this->notificationService = $notificationService;
        $this->paginator           = $paginator;
        $this->serializer          = $serializer;
        $this->entityManager       = $entityManager;
    }

    /**
     * @Route("/", methods={"get"})
     *
     * @param Request $request
     * @param AccountService $accountService
     *
     * @return JsonResponse
     */
    public function listAll(Request $request): JsonResponse
    {
        $query = $this->entityManager->getRepository(Account::class)->findAllPaginated();
        
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1));

        $data = $this->serializer->serialize($pagination, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/", methods={"post"})
     * @param Request $request
     * @param AccountService $accountService
     *
     * @return Response
     */
    public function post(Request $request, AccountService $accountService): Response
    {
        $account = $this->serializer->deserialize($request->getContent(), Account::class, 'json');

        try {
            $account = $accountService->create($account);

            $data = $this->serializer->serialize($account, 'json');

            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        } catch (InvalidEntityException $e) {
            return new JsonResponse($e->getErrors(), Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
     * @param AccountService $accountService
     * @return Response
     */
    public function comments(Request $request, Account $account, AccountService $accountService): Response
    {
        $query = $accountService->getComments($account->uuid);

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

        /** @var Notification $notification */
        foreach ($pagination->getData() as $notification) {
            $this->notificationService->read($notification);
        }

        $data = $this->serializer->serialize($pagination, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/coins", methods={"post"})
     * @param Request $request
     * @param TransactionService $transactionService
     * @return JsonResponse
     */
    public function buyCoins(Request $request, TransactionService $transactionService): JsonResponse
    {
        $account = $this->getUser()->getAccount();

        $content = json_decode($request->getContent(), true);

        if (!isset($content['amount'])) {
            return new JsonResponse(['error' => 'Tem que ser informato o campo amount'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $transactionService->credit($account, $content['amount']);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $data = $this->serializer->serialize($account, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}