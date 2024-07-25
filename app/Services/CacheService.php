<?php

namespace App\Services;
use Cache;
use Carbon\Carbon;

class CacheService
{
    /**
     * Set an item in the cache.
     *
     * @param integer $id             The identifier.
     * @param mixed   $data           The data.
     * @param Carbon  $expiryDateTime The Carbon object with the datetime representing expiry.
     *
     * @return void
     */
    public function setCacheItem($id, $data, $expiryDateTime)
    {
        Cache::put($id, $data, $expiryDateTime);
    }

    /**
     * Return the item from the cache with the given id.
     *
     * @param integer $id The identifier.
     * @return mixed|null
     */
    public function loadCacheItem($id)
    {
        return Cache::get($id);
    }
}
