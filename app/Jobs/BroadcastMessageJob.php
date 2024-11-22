<?php

namespace App\Jobs;

use App\Events\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $message,
        public string $title,
        public string $type,
        public string $second,
    ) {
        //
    }

    public function handle(): void
    {
        event(new Notification([$this->message, $this->title, $this->type, $this->second]));
    }
}
