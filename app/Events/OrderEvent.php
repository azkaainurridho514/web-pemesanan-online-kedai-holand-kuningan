<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $icon, $title, $text;

    /**
     * Create a new event instance.
     */
    public function __construct($icon, $title, $text)
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->text = $text;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('order-event'),
        ];
    }
    public function broadcastWith()
    {
        return [
            "icon" => $this->icon,
            "title" => $this->title,
            "text" => $this->text,
        ];
    }
}
