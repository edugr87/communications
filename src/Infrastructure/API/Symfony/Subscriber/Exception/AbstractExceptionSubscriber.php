<?php

namespace App\Infrastructure\API\Symfony\Subscriber\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

abstract class AbstractExceptionSubscriber
{
    /**
     * AbstractExceptionSubscriber constructor.
     */
    public function __construct()
    {
    }


    /**
     * @param ExceptionEvent $event
     * @param mixed $httpResponseCode
     * @param array|null $errors
     */
    protected function processKernelException(
        ExceptionEvent $event,
        int $httpResponseCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        array $errors = null
    ): void
    {
        $exception = $event->getThrowable();

        $response = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];

        if (1 === \getenv('APP_DEBUG')) {
            $response = \array_merge($response, [
                'class' => get_class($exception),
                'file' => $exception->getFile(),
            ]);
        }

        $event->setResponse(new JsonResponse($response, 0 !== $exception->getCode() ?
                $exception->getCode() :
                $httpResponseCode)
        );
    }
}
