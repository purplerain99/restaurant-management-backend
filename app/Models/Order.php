<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'restaurant_table_id',
        'status',
        'subtotal',
        'grand_total',
        'note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function restaurantTable(): BelongsTo
    {
        return $this->belongsTo(
            RestaurantTable::class
        );
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}