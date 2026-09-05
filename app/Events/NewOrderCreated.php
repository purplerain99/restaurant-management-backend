<?php

namespace App\Events;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderCreated implements
    ShouldBroadcast,
    ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load([
            'restaurantTable',
            'orderItems',
        ]);
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'restaurant.orders'
            ),

            new PrivateChannel(
                'kitchen.orders'
            ),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'order' => (new OrderResource(
                $this->order
            ))->resolve(),
        ];
    }
}

