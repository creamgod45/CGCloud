<?php

namespace App\Console\Commands;

use App\Events\UserNotification;
use Illuminate\Console\Command;

class BroadcastUserMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast:UserNotification {message} {title} {type} {second} {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '發送泡泡訊息給指定用戶';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        event(new UserNotification([
            $this->argument('message'),
            $this->argument('title'),
            $this->argument('type'),
            $this->argument('second'),
            $this->argument('id'),
        ]));
    }
}
