<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'tracking_token',
        'restaurant_table_id',

        'guest_name',
        'guest_phone',

        'status',

        'subtotal',
        'tax_amount',
        'service_charge',
        'grand_total',

        'note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Restaurant Table
    |--------------------------------------------------------------------------
    */
    public function restaurantTable(): BelongsTo
    {
        return $this->belongsTo(
            RestaurantTable::class,
            'restaurant_table_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Order Items
    |--------------------------------------------------------------------------
    */
    public function orderItems(): HasMany
    {
        return $this->hasMany(
            OrderItem::class,
            'order_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Allowed Next Statuses
    |--------------------------------------------------------------------------
    */
    public function allowedNextStatuses(): array
    {
        return match ($this->status) {
            'pending' => [
                'confirmed',
                'cancelled',
            ],

            'confirmed' => [
                'preparing',
                'cancelled',
            ],

            'preparing' => [
                'ready',
                'cancelled',
            ],

            'ready' => [
                'served',
            ],

            'served' => [
                'completed',
            ],

            'completed',
            'cancelled' => [],

            default => [],
        };
    }
}
