<?php

namespace App\Infrastructure\API\Symfony\Controllers\Communications;

use App\Application\Service\Ad\GetCommunicationsService\GetCommunicationsRequest;
use App\Application\Service\Ad\GetCommunicationsService\GetCommunicationsService;
use App\Domain\Model\Communications\Communications;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCommunicationsController extends AbstractController
{
    private $getCommunicationsService;
    private $serializer;

    public function __construct(GetCommunicationsService $getCommunicationsService, SerializerInterface $serializer)
    {
        $this->getCommunicationsService = $getCommunicationsService;
        $this->serializer = $serializer;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $createAdRequest =
            new GetCommunicationsRequest(
                $request->request->get('number') ?? '6111111111'
            );
        $response = $this->getCommunicationsService->execute($createAdRequest);
        if ($response instanceof Communications) {
            $response = $this->serializer->serialize($response, 'json');
        }
        return JsonResponse::fromJsonString($response);
    }
}
