<?php

namespace App\Console\Commands;

use App\Jobs\CleanTemporaryFileJob;
use Illuminate\Console\Command;

class CleanTemporaryFileCommand extends Command
{
    protected $signature = 'clean:temporary-file';

    protected $description = '清除暫存檔案';

    public function handle(): void
    {
        $cleanTemporaryFileJob = new CleanTemporaryFileJob();
        $cleanTemporaryFileJob->dispatch();
    }
}
