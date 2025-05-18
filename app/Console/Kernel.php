<?php

namespace App\Console;

use App\Jobs\CleanTemporaryFileJob;
use App\Jobs\ScanProcessingDashVideoDeadWatchDogJob;
use App\Jobs\VideoFileToDashJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->job(new BroadcastMessageJob("每五分鐘執行的廣播 Pusher", "固定廣播", "info", "10000"))->everyFiveMinutes();

        //$key = 'web_scraper_cache.txt'; // 文件名稱
        //$cahce = 1;
        //$max = Cache::remember('web_scraper_cache_max.txt', now()->addMinutes(5), function (){
        //    return Inventory::select(['id'])->count() / 24;
        //});
        //if (Storage::drive('local')->exists($key)) {
        //    $cahce = intval(Storage::drive('local')->get($key)) + 1;
        //    Storage::drive('local')->put($key, $cahce);
        //} else {
        //    Storage::drive('local')->put($key, $cahce);
        //}
        //if($max <= $cahce){
        //    Storage::drive('local')->put($key, 1);
        //}
        //$schedule->job(new CreateHomeCacheJob($cahce))->everyFiveSeconds();
        $schedule->job(new VideoFileToDashJob())->everyMinute();
        $schedule->job(new ScanProcessingDashVideoDeadWatchDogJob())->everyMinute();
        $schedule->job(new CleanTemporaryFileJob())->everyMinute();
        $schedule->command('db:backup --disk=backups')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
