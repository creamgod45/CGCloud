<?php

namespace App\Models;

use App\Casts\ExpiresAtCast;
use App\Lib\Utils\RouteNameField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use LaravelIdea\Helper\App\Models\_IH_SharePermissions_C;

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

    /**
     * @return \Illuminate\Database\Eloquent\Collection<VirtualFile> | VirtualFile[]
     */
    public function getAllVirtualFiles()
    {
        $hasMany = $this->shareTableVirtualFile();
        /** @var ShareTableVirtualFile $shareTableVirtualFile */
        $shareTableVirtualFiles = $hasMany->get();
        /** @var VirtualFile[] $virtualFiles */
        $virtualFiles = [];
        foreach ($shareTableVirtualFiles as $shareTableVirtualFile) {
            $results = $shareTableVirtualFile->virtualFile()->getResults();
            $virtualFiles [] = $results;
        }
        return \Illuminate\Database\Eloquent\Collection::make($virtualFiles);
    }

    public function shareTableVirtualFile(): HasMany
    {
        return $this->hasMany(ShareTableVirtualFile::class, 'share_table_id', 'id');
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'id', 'member_id');
    }

    public function shareTablePermission(): HasMany
    {
        return $this->hasMany(SharePermissions::class, 'share_tables_id', 'id');
    }

    public function shareURL(): string
    {
        return route(
            RouteNameField::PageShareableShareTableItem->value,
            ['shortcode' => $this->short_code],
        );
    }
}
