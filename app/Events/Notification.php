<?php

namespace App\Events;

use App\Lib\Type\String\CGStringable;
use Exception;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Notification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, Queueable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public array $message,
    ) {
        //
        try {
            Log::info("Notification Event trigger: " . new CGStringable($this->message));
        } catch (Exception $e) {
            Log::info("App\\Events\\Notification:30:13 Exception = " . $e->getMessage());
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('Notification'),
        ];
    }

    public function broadcastAs()
    {
        //命名推播的事件
        return 'Notification';
    }

    public function broadcastWith(): array
    {
        return [
            'description' => $this->message[0],
            'title' => $this->message[1],
            'type' => $this->message[2],
            'second' => $this->message[3],
        ];
    }

}
