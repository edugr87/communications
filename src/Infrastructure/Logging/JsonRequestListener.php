<?php

namespace App\Infrastructure\Logging;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class JsonRequestListener extends Event
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(LoggerInterface $logger, ContainerInterface $container)
    {
        $this->logger = $logger;
        $this->container = $container;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $this->setLanguageIntoContainerByHeader($event);

        $request = $event->getRequest();

        if (null === $request->getContentType()
            || false === strpos($request->getContentType(), 'json')
        ) {
            return;
        }

        $body = $request->getContent();

        if ('' === $body) {
            return;
        }

        $json = json_decode($body, true);
        $log = isset($this->logger) && null !== $this->logger;

        // "null" is as valid JSON response, so check the return value of json_decode AND whether the original content
        // is equal to JSON's "null" type.
        if ((null === $body && 'null' !== $body) || JSON_ERROR_NONE !== json_last_error()) {
            $error_code = json_last_error();
            $error_msg = sprintf('An error occurred while trying to decode JSON data: %s', \json_last_error_msg());

            if ($log) {
                $this->logger->info($error_msg, ['content' => $request->getContent()]);
            }

            throw new Exception($error_msg, $error_code);
        }

        if (is_array($json)) {
            $request->request->replace($json);
        }
    }

    private function setLanguageIntoContainerByHeader(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $language = $request->getPreferredLanguage();

        if (false !== ($pos = strpos($language, '_'))) {
            $language = substr($language, 0, $pos);
        }

        $this->container->set('language', $language);
    }
}
