<?php

namespace App\Models;

use App\Casts\ExpiresAtCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'filename',
        'path',
        'extension',
        'minetypes',
        'disk',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => ExpiresAtCast::class,
    ];

}
