<?php

namespace App\Models;

use App\Casts\ExpiresAtCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RahulHaque\Filepond\Models\Filepond;

class ShareTable extends Model
{
    protected $fillable = [
        'filepond_id',
        'member_id',
        'name',
        'description',
        'type',
        'expired_at',
        'short_code',
        'secret',
    ];

    protected $casts = [
        'expires_at' => ExpiresAtCast::class,
    ];

    use HasFactory;

    public function virtualFile(): BelongsTo
    {
        return $this->belongsTo(VirtualFile::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
