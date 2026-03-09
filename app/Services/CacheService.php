<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CacheService
{

    protected static $redisAvailable = null;

    protected static function isRedisAvailable()
    {
        try {
            return Redis::ping() ? true : false;
        } catch (\Exception $e) {
            Log::warning("Redis is not available: " . $e->getMessage());
            return false;
        }
    }

    public static function remember($cacheKey, $ttl = 60, $callback)
    {
        try {
            // Check Redis connectivity only once
            if (is_null(self::$redisAvailable)) {
                self::$redisAvailable = self::isRedisAvailable();
            }

            if (self::$redisAvailable) {
                return Cache::store('redis')->remember($cacheKey, $ttl, $callback);
            }
        } catch (\Exception $e) {
            Log::warning("Redis error: " . $e->getMessage());
        }

        return Cache::remember($cacheKey, $ttl,  $callback);
    }

    public static function has($cacheKey)
    {
        // Check Redis connectivity only once
        try {
            if (is_null(self::$redisAvailable)) {
                self::$redisAvailable = self::isRedisAvailable();
            }

            if (self::$redisAvailable) {
                return Cache::store('redis')->has($cacheKey);
            }
        } catch (\Exception $e) {
            Log::warning("Redis error: " . $e->getMessage());
        }

        return Cache::has($cacheKey);
    }

    public static function get($cacheKey)
    {

        try {
            if (is_null(self::$redisAvailable)) {
                self::$redisAvailable = self::isRedisAvailable();
            }

            if (self::$redisAvailable) {
                return Cache::store('redis')->get($cacheKey);
            }
        } catch (\Exception $e) {
            Log::warning("Redis error: " . $e->getMessage());
        }

        return Cache::get($cacheKey);
    }

    public static function delete($cacheKey)
    {
        try {
            if (is_null(self::$redisAvailable)) {
                self::$redisAvailable = self::isRedisAvailable();
            }

            if (self::$redisAvailable) {
                return Cache::store('redis')->delete($cacheKey);
            }
        } catch (\Exception $e) {
            Log::warning("Redis error: " . $e->getMessage());
        }

        return Cache::delete($cacheKey);
    }

    // Method untuk menyimpan cache dengan prefix, mendukung Redis dan store lain
    public static function putWithPrefix($prefix, $key, $ttl = 60, $value)
    {
        $fullKey = $prefix . $key;

        try {
            if (is_null(self::$redisAvailable)) {
                self::$redisAvailable = self::isRedisAvailable();
            }

            if (self::$redisAvailable) {
                return Cache::store('redis')->remember($fullKey, $ttl, $value);
            }
        } catch (\Exception $e) {
            Log::warning("Redis error: " . $e->getMessage());
        }

        $value = Cache::put($fullKey, $value, $ttl);

        // Jika tidak menggunakan Redis, simpan key dalam daftar keys
        $allKeys = Cache::get($prefix . '_keys', []);
        $allKeys[] = $fullKey;
        Cache::put($prefix . '_keys', $allKeys, $ttl);

        return $value;
    }

    // Method untuk menghapus cache berdasarkan prefix
    public static function deleteByPrefix($prefix)
    {

        // Jika menggunakan Redis, gunakan Redis commands untuk menghapus keys dengan prefix

        try {
            if (is_null(self::$redisAvailable)) {
                self::$redisAvailable = self::isRedisAvailable();
            }

            if (self::$redisAvailable) {
                $databasePrefix = config('database.redis.options.prefix');
                $cachePrefix = config('cache.prefix');

                $basePrefix = $databasePrefix . $cachePrefix;
                $prefix = $basePrefix . $prefix . ':*';

                $cursor = '0';

                do {
                    [$cursor, $keys] = Redis::scan($cursor, 'MATCH', $prefix);

                    foreach ($keys as $key) {
                        $key = str_replace($basePrefix, '', $key); // Menghapus prefix database Redis
                        self::delete($key);
                    }
                } while ($cursor !== '0');

                return;
            }
        } catch (\Exception $e) {
            Log::warning("Redis error: " . $e->getMessage());
        }

        // Jika tidak menggunakan Redis, gunakan metode manual
        $keys = Cache::get($prefix . '_keys', []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Hapus juga daftar keys
        Cache::forget($prefix . '_keys');
    }
}
