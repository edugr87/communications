<?php

namespace App\Infrastructure\API\Symfony\Subscriber\Exception;

use App\Domain\Exceptions\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ValidationExceptionSubscriber extends AbstractExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                'onKernelException', 10,
            ],
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if (!($exception instanceof ValidationException)) {
            return;
        }

        $event->setResponse(new JsonResponse([
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
