<?php

namespace App\Models;

use App\Casts\ExpiresAtCast;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

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


    public function getPublicUrl()
    {
        return Storage::url($this->path);
    }


    public function getTemporaryUrl(DateTimeInterface $expiration = null)
    {
        if($expiration === null) {
            $expiration = now()->addDays();
        }
        return Storage::temporaryUrl($this->path, $expiration);
    }

}
