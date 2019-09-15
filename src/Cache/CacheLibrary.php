<?php

namespace App\Cache;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheLibrary
{
    /**
     * @var AdapterInterface
     */
    private $redisCache;

    public function __construct(AdapterInterface $redisCache)
    {
        $this->redisCache = $redisCache;
    }

    public function set($key, $value)
    {
        $item = $this->redisCache->getItem($key);
        $item->set($value);
        $this->redisCache->save($item);
    }

    public function get($key)
    {
        $item = $this->redisCache->getItem($key);

        if ($item->isHit()) {
            return $item->get();
        }

        return null;
    }

    public function delete($key)
    {
        $this->redisCache->deleteItem($key);
    }
}