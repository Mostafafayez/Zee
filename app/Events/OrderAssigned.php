<?php
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderAssigned implements ShouldBroadcast
{
    use SerializesModels;

    public $order;

public function __construct(Order $order)
{
    $this->order = $order;

    \Log::info('OrderAssigned event fired for order: ' . $this->order->track_number);
}


    public function broadcastOn()
    {
        return new Channel('courier.' . $this->order->courier_id);
    }

    public function broadcastWith(): array
    {
        return [
            // 'id' => $this->order->id,
            'status' => $this->order->status,
            'order_data' => $this->order, // Customize as needed
        ];
    }

    public function broadcastAs()
    {
        return 'order.assigned';
    }
}
