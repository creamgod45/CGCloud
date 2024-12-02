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


    public function getTemporaryUrl(DateTimeInterface $expiration = null)
    {
        if($expiration === null) {
            $expiration = now()->addMinutes ();
        }
        $temporaryUrl = URL::temporarySignedRoute(
            RouteNameField::APIPreviewFileTemporary->value,
            $expiration,
            [ 'fileId' => $this->uuid ]
        );
        return $temporaryUrl;
    }

}
