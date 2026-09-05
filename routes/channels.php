<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('restaurant.orders', function ($user) {
    return in_array($user->role, [
        'admin',
        'staff',
    ]);
});

Broadcast::channel('kitchen.orders', function ($user) {
    return in_array($user->role, [
        'admin',
        'staff',
    ]);
});