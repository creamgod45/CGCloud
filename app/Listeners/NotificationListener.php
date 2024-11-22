<?php

namespace App\Listeners;

use App\Events\Notification;

class NotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Notification $event): void
    {
        //
        //$options = array(
        //    'cluster' => env('PUSHER_APP_CLUSTER'),
        //    'useTLS' => true
        //);
        //try {
        //    $pusher = new Pusher(
        //        env('PUSHER_APP_KEY'),
        //        env('PUSHER_APP_SECRET'),
        //        env('PUSHER_APP_ID'),
        //        $options
        //    );
        //    $pusher->trigger($event->broadcastOn()[0], $event->broadcastAs(), $event->message);
        //} catch (Exception $e) {
        //    Log::error($e->getMessage());
        //}
    }
}
