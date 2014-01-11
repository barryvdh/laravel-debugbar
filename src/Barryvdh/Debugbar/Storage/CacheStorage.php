<?php
namespace Barryvdh\Debugbar\Storage;

use DebugBar\Storage\StorageInterface;
use Illuminate\Cache\CacheManager;

/**
 * Stores collected data into files
 */
class CacheStorage implements StorageInterface
{

    /**
     * The cache manager instance.
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * @param \Illuminate\Cache\CacheManager $cache The CacheManager
     */
    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function save($id, $data)
    {
        $this->cache->put($this->makeKey($id), $data, 1);
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        $key = $this->makeKey($id);
        $data = $this->cache->get($key);
        $this->cache->offsetUnset($key);
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function find(array $filters = array(), $max = 20, $offset = 0)
    {
        //Cannot be done with Cache?
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->cache->flush();
    }

    public function makeKey($id)
    {
        return "debugbar.".$id;
    }
}