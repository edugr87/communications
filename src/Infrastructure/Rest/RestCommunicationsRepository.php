<?php

namespace App\Infrastructure\Rest;

use App\Domain\Exceptions\NotSuccessfulRequestException;
use App\Domain\Model\Communications\CommunicationsRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class RestCommunicationsRepository implements CommunicationsRepository
{
    private $client;

    public  function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function request(string $string)
    {
        try {
            $file = sprintf('communications.%s.log', $string);
            $response = $this->client->request('GET',
                'http://gist.githubusercontent.com/miguelgf/e099e5e5bfde4f6428edb0ae94946c83/raw/fa27e59eb8f14ee131fca5c0c7332ff3b924e0b2'
            );
        } catch (ClientException $exception) {
            throw new NotSuccessfulRequestException('Error getting the data');
        }
        $body = $response->getBody()->getContents();
        return $body;
    }

    public function all(): array
    {
        // TODO: Implement all() method.
    }

    public function byNumber(string $name)
    {
      $response = $this->request($name);
      return $response;
    }
}