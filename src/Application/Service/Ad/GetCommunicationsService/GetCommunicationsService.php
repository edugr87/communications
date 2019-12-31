<?php

namespace App\Application\Service\Ad\GetCommunicationsService;

use App\Application\Service\ApplicationService;
use App\Domain\Model\Communications\Call;
use App\Domain\Model\Communications\Communication;
use App\Domain\Model\Communications\Communications;
use App\Domain\Model\Communications\CommunicationsRepository;
use App\Domain\Model\Communications\Sms;
use App\Domain\Service\CacheInterface;

final class GetCommunicationsService implements ApplicationService
{

    private $communicationsRepository;
    private $redisBaseRepository;

    public function __construct(CommunicationsRepository $communicationsRepository, CacheInterface $redisBaseRepository)
    {
        $this->communicationsRepository = $communicationsRepository;
        $this->redisBaseRepository = $redisBaseRepository;
    }

    /**
     * @param GetCommunicationsRequest $request
     * @return Communications
     */
    public function execute($request = null)
    {
        $response = $this->communicationsRepository->byNumber($request->number());
        $length = strlen($response);

        if ($this->redisBaseRepository->has($request->number() . '_' . $length)) {
            return $this->redisBaseRepository->get($request->number() . '_' . $length, Communications::class);
        }

        $communications = $this->ProcessResponseToGetCommunications($request, $response);

        $this->redisBaseRepository->set($communications->telephoneNumber() . '_' . $length, $communications, 3600);
        return $communications;
    }

    /**
     * @param $request
     * @param $response
     * @return Communications
     */
    private function ProcessResponseToGetCommunications($request, $response): Communications
    {
        $communications = new Communications($request->number());

        foreach (explode("\n", $response) as $communicationRow) {
            $type = substr($communicationRow, 0, 1);
            if (in_array($type, ['C', 'S'])) {

                list($number1, $number2, $incoming, $name, $date, $duration) = $this->GetVariablesForRowCommunication($communicationRow);

                $contactNumber = $incoming === '0' ? $number2 : $number1;

                $communication = ($communications->hasCommunication($contactNumber)) ?
                    $communications->getCommunication($contactNumber)
                    : new Communication($contactNumber);

                if ($type === 'C') {
                    $communication->addCalls(new Call(
                        $incoming === '0',
                        $name,
                        $date,
                        $duration
                    ));
                } else {
                    $communication->addSms(new Sms(
                        $incoming === '0',
                        $name,
                        $date
                    ));
                }
                $communications->addCommunication($communication);
            }
        }
        return $communications;
    }

    private function GetVariablesForRowCommunication($communicationRow): array
    {
        $number1 = substr($communicationRow, 1, 9);
        $number2 = substr($communicationRow, 10, 9);
        $incoming = substr($communicationRow, 19, 1);
        $name = substr($communicationRow, 20, 24);
        $date = substr($communicationRow, 44, 14);
        $duration = substr($communicationRow, 58, 6);
        return array($number1, $number2, $incoming, $name, $date, $duration);
    }

}