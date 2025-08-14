<?php

// app/Events/OrderAssigned.php
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\{Channel, PrivateChannel, PresenceChannel};
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Now = no queue needed
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;


class OrderAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $order;
    public function __construct(  $order) {

        $this->order = $order;

    }

    public function broadcastOn()
    {
       return new Channel("courier_zee");

    }

    public function broadcastAs(): string
    {
        return 'order.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => $this->order,

        ];
    }
}

