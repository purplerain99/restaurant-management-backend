<?php

namespace App\Events;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
        public ?string $oldStatus = null
    ) {
        $this->order->loadMissing([
            'restaurantTable',
            'orderItems',
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('restaurant.orders'),

            new PrivateChannel('kitchen.orders'),

            new Channel(
                'orders.' .
                $this->order->tracking_token
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,

                'order_number' =>
                    $this->order->order_number,

                'tracking_token' =>
                    $this->order->tracking_token,

                'status' =>
                    $this->order->status,

                'old_status' =>
                    $this->oldStatus,

                'updated_at' =>
                    $this->order
                        ->updated_at
                        ?->toISOString(),

                'restaurant_table' => $this->order
                    ->restaurantTable
                    ?->toArray(),

                'order_items' => $this->order
                    ->orderItems
                    ->toArray(),

                'guest_name' =>
                    $this->order->guest_name,

                'guest_phone' =>
                    $this->order->guest_phone,

                'subtotal' =>
                    $this->order->subtotal,

                'tax_amount' =>
                    $this->order->tax_amount,

                'service_charge' =>
                    $this->order->service_charge,

                'grand_total' => (float) $this->order->grand_total,
            ],
        ];
    }
}