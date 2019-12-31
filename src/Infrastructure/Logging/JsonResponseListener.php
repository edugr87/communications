<?php

namespace App\Infrastructure\Logging;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class JsonResponseListener extends Event
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * JsonResponseListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();

        $event->setResponse($this->parseResponse($response));
    }

    /**
     * @param $response
     *
     * @throws \UnexpectedValueException
     *
     * @return Response
     */
    private function parseResponse(Response $response): Response
    {
        $parsedArray = [];
        $message = 'JSON response sent to apps';
        if (Response::HTTP_FOUND === $response->getStatusCode()) {
            /** @var RedirectResponse $response */
            $message = 'Redirecting to '.''.$response->getTargetUrl();
        }

        $this->logResponse($message, $parsedArray);

        return $response;
    }

    /**
     * @param $message
     * @param $parsedArray
     */
    private function logResponse(string $message, array $parsedArray): void
    {
        $this->logger->debug(
            $message,
            [
                'Response' => $parsedArray,
            ]
        );
    }
}
