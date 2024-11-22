<?php

namespace App\Console\Commands;

use App\Events\Notification;
use Illuminate\Console\Command;

class BroadcastMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast:Notification {message} {title} {type} {second}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '發送泡泡訊息給全部訪問者';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        event(new Notification([
            $this->argument('message'),
            $this->argument('title'),
            $this->argument('type'),
            $this->argument('second'),
        ]));
    }
}
