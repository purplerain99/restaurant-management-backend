<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'order_number' => $this->order_number,
            'tracking_token' => $this->tracking_token,

            'status' => $this->status,

            'guest_name' => $this->guest_name,
            'guest_phone' => $this->guest_phone,

            'note' => $this->note,

            'table' => $this->whenLoaded('restaurantTable', function () {
                return [
                    'id' => $this->restaurantTable->id,
                    'name' => $this->restaurantTable->name,
                    'table_code' => $this->restaurantTable->table_code,
                ];
            }),

            'items' => OrderItemResource::collection(
                $this->whenLoaded('orderItems')
            ),

            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'tax_rate' => 5,

            'service_charge' => $this->service_charge,
            'service_charge_rate' => 10,

            'grand_total' => $this->grand_total,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}