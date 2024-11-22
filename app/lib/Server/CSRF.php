<?php

namespace App\Lib\Server;

use App\Lib\Utils\EncryptedCache;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CSRF
{
    public array|string $key;

    public function __construct(array|string $key)
    {
        $this->key = $key;
        if (!$this->has()) {
            EncryptedCache::put($this->key, Str::random(30), 86400);
        }
    }

    public function has(): bool
    {
        return Cache::has($this->key);
    }

    /**
     * @param array|string $key
     * @param mixed        $value
     *
     * @return bool
     * @throws Exception
     */
    public static function equalManual(array|string $key, mixed $value): bool
    {
        if ($value === null) {
            throw new Exception('$value is not a Nullable variable');
        }
        if (Cache::has($key)) {
            $value1 = EncryptedCache::get($key);
            return $value1 === $value;
        }
        throw new Exception('$key is not in the container');
    }

    public function isEmpty(): bool
    {
        if (!$this->has()) {
            return true;
        }
        if (empty($this->get())) {
            return true;
        }
        return false;
    }

    public function get()
    {
        return EncryptedCache::get($this->key);
    }

    public function reset(): static
    {
        EncryptedCache::put($this->key, Str::random(30), 86400);
        return $this;
    }

    public function release(): static
    {
        Cache::delete($this->key);
        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     * @throws Exception
     */
    public function equal(mixed $value): bool
    {
        if ($value === null) {
            throw new Exception('$value is not a Nullable variable');
        }
        return $this->get() === $value;
    }
}
