<?php

namespace App\Jobs;

use App\Models\VirtualFile;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanTemporaryFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        Log::info('Jobs: CleanTemporaryFileJob');
        Log::info('Start Time: ' . $this->currentTime());
        $virtualFiles = VirtualFile::where('type', '=', 'temporary')
            ->where('expired_at', '<=', time())
            ->limit(50)
            ->get()
            ->all();
        foreach ($virtualFiles as $virtualFile) {
            Log::info('Delete:'."[disk]".$virtualFile->disk."[type]".$virtualFile->type.'[Path]' . $virtualFile->path);
            Storage::disk($virtualFile->disk)->delete($virtualFile->path);
            $directory = dirname($virtualFile->path);
            Storage::disk($virtualFile->disk)->deleteDirectory($directory);
            $virtualFile->delete();
        }
        Log::info('End Time: ' . $this->currentTime());
    }
}
