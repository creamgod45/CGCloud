<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // 註冊任務處理成功的監聽器
        Queue::after(function (JobProcessed $event) {
            // 記錄成功的任務
            Log::info('任務處理成功', [
                'connectionName' => $event->connectionName,
                'job' => $event->job->getName(),
                'jobId' => $event->job->getJobId(),
                'payload' => $event->job->payload(),
            ]);
        });

        // 註冊任務失敗的監聽器
        Queue::failing(function (JobFailed $event) {
            // 記錄失敗的任務
            Log::error('任務處理失敗', [
                'connectionName' => $event->connectionName,
                'job' => $event->job->getName(),
                'jobId' => $event->job->getJobId(),
                'exception' => $event->exception->getMessage(),
                'payload' => $event->job->payload(),
            ]);
        });
    }
}
