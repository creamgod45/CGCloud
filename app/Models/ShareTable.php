<?php

namespace App\Models;

use App\Casts\ExpiresAtCast;
use App\Lib\Utils\RouteNameField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use LaravelIdea\Helper\App\Models\_IH_SharePermissions_C;
use RahulHaque\Filepond\Models\Filepond;

class ShareTable extends Model
{
    protected $fillable = [
        'member_id',
        'name',
        'description',
        'type',
        'expired_at',
        'short_code',
        'secret',
    ];

    protected $casts = [
        'expired_at' => ExpiresAtCast::class,
    ];

    use HasFactory;

    public function getAllVirtualFiles()
    {
        $allRelatedIds = $this->virtualFiles()->allRelatedIds();
        return VirtualFile::whereIn('uuid', $allRelatedIds)->get();
    }

    public function virtualFiles(): BelongsToMany
    {
        return $this->belongsToMany(VirtualFile::class, 'share_table_virtual_file');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }


    /**
     * @return SharePermissions[]|_IH_SharePermissions_C
     */
    public function shareTablePermission()
    {
        $permissions = SharePermissions::where('share_tables_id', '=', $this->id)->get();
        return $permissions;
    }

    public function shareURL(): string
    {
        return URL::temporarySignedRoute(
            RouteNameField::PageShareableShareTableItem->value,
            now()->addDays(),
            ['id' => $this->id],
        );
    }
}
