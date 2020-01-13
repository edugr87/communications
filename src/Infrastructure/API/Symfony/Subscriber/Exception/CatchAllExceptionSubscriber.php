<?php

namespace App\Infrastructure\API\Symfony\Subscriber\Exception;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CatchAllExceptionSubscriber extends AbstractExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                'onKernelException', 0,
            ],
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $this->processKernelException($event, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
