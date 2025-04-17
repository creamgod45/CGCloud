<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Carbon\Carbon;

class ExpiresAtCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return $value == -1 ? -1 : Carbon::createFromTimestamp($value ?? 0);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
