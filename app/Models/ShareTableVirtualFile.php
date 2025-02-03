<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class ShareTableVirtualFile extends Model
{
    use AsPivot;

    protected $table = 'share_table_virtual_file';

    protected $fillable = [
        'share_table_id',
        'virtual_file_uuid',
        'dash_videos_id',
    ];

    public function shareTable(): BelongsTo
    {
        return $this->belongsTo(ShareTable::class, 'share_table_id', 'id');
    }

    public function virtualFile(): BelongsTo
    {
        return $this->belongsTo(VirtualFile::class, 'virtual_file_uuid', 'uuid');
    }

    public function dashVideos(): BelongsTo
    {
        return $this->belongsTo(DashVideos::class, 'dash_videos_id', 'id');
    }

    public function isCreateDashVideo(): bool
    {
        return $this->dashVideos()->exists();
    }

    public function isAvailableDashVideo(): bool
    {
        if ($this->isCreateDashVideo()) {
            /** @var DashVideos $dashVideos */
            $dashVideos = $this->dashVideos()->getResults();
            if($dashVideos !== null){
                return $dashVideos->path !== null;
            }
        }
        return false;
    }
}
