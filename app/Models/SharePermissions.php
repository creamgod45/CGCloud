<?php

namespace App\Models;

use App\Casts\ExpiresAtCast;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class  SharePermissions extends Model
{
    protected $fillable = [
        'share_tables_id',
        'member_id',
        'permission_type',
        'expired_at',
        'start_at',
        'end_at',
    ];

    use HasFactory;

    protected $casts = [
        'expired_at' => ExpiresAtCast::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function isOwner(Member $member): bool
    {
        return $this->member_id === $member->id;
    }

    /**
     * @param Member $member
     *
     * @return SharePermissions[]|\LaravelIdea\Helper\App\Models\_IH_SharePermissions_C
     */
    public static function memberRelation(Member $member) {
        return self::where('member_id', $member->id)->get();
    }

    public function shareTable(): BelongsTo
    {
        return $this->belongsTo(ShareTable::class, 'share_tables_id', 'id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }
}
