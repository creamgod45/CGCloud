<?php

namespace App\Models;

use App\Casts\ExpiresAtCast;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharePermissions extends Model
{
    protected $fillable = [
        'share_table_id',
        'member_id',
        'permission_type',
        'expires_at',
    ];

    use HasFactory;

    protected $casts = [
        'expires_at' => ExpiresAtCast::class,
    ];

    public function shareTable(): BelongsTo
    {
        return $this->belongsTo(ShareTable::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
