<?php

namespace App\Jobs;

use App\Models\DashVideos;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ScanProcessingDashVideoDeadWatchDogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var DashVideos[]|Collection
     */
    private array $dashVideos;

    public function __construct()
    {
        $this->dashVideos = DashVideos::where('type', '=', 'processing')->limit(50)->get()->all();
    }

    public function handle(): void
    {
        Log::info("[JOBS]workDir: " . getcwd());
        Log::info("[JOBS]ScanProcessingDashVideoDeadWatchDogJob Start");
        foreach ($this->dashVideos as $d) {
            $hasPending = Cache::has('pending_process_' . $d->id);
            $hasWaterMark = Cache::has('ffmpeg_watermark_progress_' . $d->id);
            $hasStreaming = Cache::has('ffmpeg_streaming_progress_' . $d->id);
            if(!$hasWaterMark && !$hasStreaming && !$hasPending){
                $d->update([
                    'type' => 'failed',
                ]);
                Log::info("[JOBS]ScanProcessingDashVideoDeadWatchDogJob update type to failed");
            }
        }
        Log::info("[JOBS]ScanProcessingDashVideoDeadWatchDogJob end");
    }
}
