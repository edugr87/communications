<?php


namespace App\Domain\Service;

interface CacheInterface
{
    /**
     * @param string $key
     * @param $data
     * @param int $ttl
     */
    public function set(string $key, $data, int $ttl): void;

    /**
     * @param string $key
     * @param string $class
     * @return mixed
     */
    public function get(string $key, string $class);


    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @param string $keys
     * @return int
     */
    public function getTtl(string $keys): int;
}