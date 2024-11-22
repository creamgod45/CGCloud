<?php

namespace App\Lib\Utils;

use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class EncryptedCache
{
    /**
     * @param array|string                            $key
     * @param mixed                                   $data
     * @param DateInterval|DateTimeInterface|int|null $ttl
     *
     * @return void
     */
    public static function put(array|string $key, mixed $data, DateInterval|DateTimeInterface|int|null $ttl): void
    {
        $encryptedData = Crypt::encrypt($data);
        Cache::put($key, $encryptedData, $ttl);
    }

    /**
     * @param array|string $key
     *
     * @return mixed|null
     */
    public static function get(array|string $key): mixed
    {
        $encryptedData = Cache::get($key);

        if ($encryptedData) {
            return Crypt::decrypt($encryptedData);
        }

        return null;
    }
}
