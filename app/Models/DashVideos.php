<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DashVideos extends Model
{
    use HasFactory;

    protected $fillable = [
        'virtual_file_uuid',
        'share_table_virtual_file_id',
        'member_id',
        'thumb_virtual_file_uuid',
        'path',
        'filename',
        'extension',
        'disk',
        'size',
        'type',
        'format',
        'audioCodec',
        'videoCodec',
        'width',
        'height',
        'framerate',
        'bitrate',
        'duration',
        'channels',
        'sampleRate',
        'videoFrames',
        'metadata',
        'videoStream',
        'audioStream',
    ];

    public function thumbVirtualFile(): HasOne
    {
        return $this->hasOne(VirtualFile::class, 'uuid', 'thumb_virtual_file_uuid');
    }

    public function virtualFile(): HasOne
    {
        return $this->hasOne(VirtualFile::class, 'uuid', 'virtual_file_uuid');
    }

    public function shareTableVirtualFile(): BelongsTo
    {
        return $this->belongsTo(ShareTableVirtualFile::class, 'share_table_virtual_file_id', 'id');
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function isCreateDashVideo(): bool
    {
        return $this->path !== null;
    }

    public function isSuccess(): bool
    {
        return $this->type === "success";
    }

    public function isProcessing(): bool
    {
        return $this->type === "processing";
    }

    public function isWait(): bool
    {
        return $this->type === "wait";
    }

    public function isFail(): bool
    {
        return $this->type === "failed";
    }
}
