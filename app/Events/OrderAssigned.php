<?php

// app/Events/OrderAssigned.php
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Now = no queue needed
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;


class OrderAssigned implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        // private-courier.{id}
        return [new PrivateChannel('courier.' . $this->order->courier_id)];
    }

    public function broadcastAs(): string
    {
        return 'order.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id'      => $this->order->id,
            'track_number'  => $this->order->track_number,
            'status'        => $this->order->status,
            'address'       => $this->order->address,
            'total_price'   => $this->order->total_price,
            'assigned_at'   => now()->toISOString(),
        ];
    }
}

