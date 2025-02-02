<?php

namespace App\Models;

use App\Casts\ExpiresAtCast;
use App\Lib\Type\Image;
use App\Lib\Utils\RouteNameField;
use DateTimeInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class VirtualFile extends Model
{
    use HasFactory;
    use MassPrunable;

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

    /**
     * 获取可修剪模型查询构造器。
     */
    public function prunable(): VirtualFile
    {
        return static::where('expired_at', '<=', now())->where('type', '=', 'temporary');
    }

    public function members()
    {
        return $this->belongsTo(Member::class);
    }

    protected $casts = [
        'expired_at' => ExpiresAtCast::class,
    ];

    public function deleteEntry(){
        Storage::disk($this->disk)->delete($this->path);
        $this->delete();
    }

    public function shareTables()
    {
        return $this->belongsTo(ShareTableVirtualFile::class, 'uuid', 'virtual_file_uuid');
    }

    public function getPublicUrl()
    {
        return Storage::url($this->path);
    }

    public function getFileSystem(): Filesystem
    {
        return Storage::disk($this->disk);
    }

    public function getImage($shareTableId): Image
    {
        $manager = new ImageManager(new Driver());
        $filesystem = $this->getFileSystem();
        $image = $manager->read($filesystem->readStream($this->path));
        $internalImage = new Image($this->getTemporaryUrl(null, $shareTableId), $image->size()->width(),
            $image->size()->height(), $this->minetypes, "", "", image: $image);
        return $internalImage;
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
