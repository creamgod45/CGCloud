<?php

namespace App\Models;

use App\Casts\ExpiresAtCast;
use App\Lib\Utils\RouteNameField;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class VirtualFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'members_id',
        'filename',
        'path',
        'extension',
        'minetypes',
        'disk',
        'expired_at',
        'size',
        'type',
    ];

    public function members()
    {
        return $this->belongsTo(Member::class);
    }

    protected $casts = [
        'expired_at' => ExpiresAtCast::class,
    ];

    public function shareTables()
    {
        return $this->belongsToMany(ShareTable::class, 'share_table_virtual_file');
    }


    public function getPublicUrl()
    {
        return Storage::url($this->path);
    }


    public function getTemporaryUrl(DateTimeInterface $expiration = null, $shareTableId = null)
    {
        if($expiration === null) {
            $expiration = now()->addMinutes();
        }
        $temporaryUrl = "";

        if($shareTableId === null) {
            $temporaryUrl = URL::temporarySignedRoute(
                RouteNameField::APIPreviewFileTemporary->value,
                $expiration,
                ['fileId' => $this->uuid],
            );
        } else {
            $temporaryUrl = URL::temporarySignedRoute(
                RouteNameField::APIPreviewFileTemporary2->value,
                $expiration,
                [
                    'fileId' => $this->uuid,
                    'shareTableId' => $shareTableId,
                ],
            );
        }
        return $temporaryUrl;
    }

}
