<?php

namespace App\Services;

use App\Events\NewOrderCreated;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create a new customer order.
     *
     * Tax = 5%
     * Service Charge = 10%
     */
    public function createOrder(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Find Table
            |--------------------------------------------------------------------------
            */

            $table = RestaurantTable::query()
                ->where('table_code', $data['table_code'])
                ->lockForUpdate()
                ->first();

            if (!$table) {
                throw ValidationException::withMessages([
                    'table_code' => [
                        'စားပွဲအချက်အလက် မတွေ့ပါ။',
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Prepare Order Items
            |--------------------------------------------------------------------------
            */

            $orderItems = [];
            $subtotal = 0;

            foreach ($data['items'] as $itemData) {

                $menuItem = MenuItem::query()
                    ->where('id', $itemData['menu_item_id'])
                    ->where('is_available', true)
                    ->lockForUpdate()
                    ->first();

                if (!$menuItem) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Menu item #{$itemData['menu_item_id']} မရရှိတော့ပါ။",
                        ],
                    ]);
                }

                $quantity = (int) $itemData['quantity'];

                /*
                |--------------------------------------------------------------------------
                | IMPORTANT
                |--------------------------------------------------------------------------
                | Frontend က price မပို့ခိုင်းဘဲ
                | Database ထဲက current price ကိုပဲ အသုံးပြုမယ်။
                |--------------------------------------------------------------------------
                */

                $unitPrice = (float) $menuItem->price;

                $lineTotal = round(
                    $unitPrice * $quantity,
                    2
                );

                $subtotal += $lineTotal;

                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,
                    'menu_item_name' => $menuItem->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineTotal,
                    'special_note' => $itemData['special_note'] ?? null,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Tax & Service Charge
            |--------------------------------------------------------------------------
            */

            $subtotal = round($subtotal, 2);

            $taxAmount = round(
                $subtotal * 0.05,
                2
            );

            $serviceCharge = round(
                $subtotal * 0.10,
                2
            );

            $grandTotal = round(
                $subtotal
                    + $taxAmount
                    + $serviceCharge,
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Generate Order Number
            |--------------------------------------------------------------------------
            |
            | Example:
            | ORD-20260903-0001
            |
            */

            $orderNumber = $this->generateOrderNumber();

            /*
            |--------------------------------------------------------------------------
            | Generate Tracking Token
            |--------------------------------------------------------------------------
            */

            $trackingToken = 'trk_' . Str::random(48);

            /*
            |--------------------------------------------------------------------------
            | Create Order
            |--------------------------------------------------------------------------
            */

            $order = Order::create([
                'order_number' => $orderNumber,
                'tracking_token' => $trackingToken,
                'restaurant_table_id' => $table->id,

                'guest_name' => $data['guest_name'] ?? null,
                'guest_phone' => $data['guest_phone'] ?? null,

                'status' => 'pending',

                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'service_charge' => $serviceCharge,
                'grand_total' => $grandTotal,

                'note' => $data['note'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Order Items
            |--------------------------------------------------------------------------
            */

            foreach ($orderItems as $item) {
                $order->orderItems()->create($item);
            }

            /*
            |--------------------------------------------------------------------------
            | Occupy Table
            |--------------------------------------------------------------------------
            */

            if ($table->status === 'available') {
                $table->update([
                    'status' => 'occupied',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Broadcast New Order
            |--------------------------------------------------------------------------
            */

            event(new NewOrderCreated($order));

            /*
            |--------------------------------------------------------------------------
            | Return Complete Order
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | Relationship name is orderItems, NOT items.
            |--------------------------------------------------------------------------
            */

            return $order->load([
                'restaurantTable',
                'orderItems',
            ]);
        });

        return $order;
    }

    /**
     * Generate unique daily order number.
     *
     * Example:
     * ORD-20260903-0001
     */
    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');

        $lastOrder = Order::query()
            ->whereDate(
                'created_at',
                now()->toDateString()
            )
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $sequence = $lastOrder
            ? ((int) substr($lastOrder->order_number, -4)) + 1
            : 1;

        return sprintf(
            'ORD-%s-%04d',
            $date,
            $sequence
        );
    }
}