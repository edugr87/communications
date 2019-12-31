<?php


namespace App\Infrastructure\Persistence\Redis;


use App\Domain\Service\CacheInterface;
use JMS\Serializer\SerializerInterface;
use Predis\ClientInterface;

class RedisBaseRepository implements CacheInterface
{
    public const FORMAT = 'json';
    public const REPOSITORY_TYPE = 'CACHE';
    private const PREFIX_KEY = 'finizens';

    /**
     * @var ClientInterface
     */
    protected $cacheService;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(
        ClientInterface $cacheService,
        SerializerInterface $serializer
    )
    {
        $this->cacheService = $cacheService;
        $this->serializer = $serializer;
    }

    public function set(string $key, $data, int $ttl = 0): void
    {
        $this->cacheService->set(self::PREFIX_KEY . $key, $this->serializer->serialize($data, self::FORMAT));
        if ($ttl) {
            $this->cacheService->expire('finizens' . $key, $ttl);
        }
    }

    public function get(string $key, string $class)
    {
        $data = $this->cacheService->get(self::PREFIX_KEY . $key);
        return $data; //$this->serializer->deserialize($data, $class, self::FORMAT);
    }

    public function has(string $key): bool
    {
        if (!(bool)$this->cacheService->exists(self::PREFIX_KEY . $key)) {
            return false;
        }

        return true;
    }

    public function getTtl(string $keys): int
    {
        return $this->cacheService->ttl(self::PREFIX_KEY . $keys);
    }
}