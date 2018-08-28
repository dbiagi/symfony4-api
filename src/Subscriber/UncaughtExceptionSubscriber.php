<?php declare(strict_types=1);

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UncaughtExceptionSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $environment;

    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['serializeException', 10],
            ],
        ];
    }

    public function serializeException(GetResponseForExceptionEvent $event): void
    {
        $e = $event->getException();

        $content = [
            'message' => $e->getMessage(),
        ];

        if ($this->environment === 'dev') {
            $content['stack_trace'] = $e->getTrace();
        }

        $event->setResponse(new JsonResponse($content, Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}