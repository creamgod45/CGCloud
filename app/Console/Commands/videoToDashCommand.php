<?php

namespace App\Console\Commands;

use App\Jobs\VideoFileToDashJob;
use Illuminate\Console\Command;

class videoToDashCommand extends Command
{
    protected $signature = 'video:to-dash';

    protected $description = '轉換 VirtualFile 至 DashVideo';

    public function handle(): void
    {
        $videoFileToDashJob = new VideoFileToDashJob();
        $videoFileToDashJob->dispatch();
    }
}
