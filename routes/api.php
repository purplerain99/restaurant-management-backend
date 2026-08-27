<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'Restaurant API v1 အလုပ်လုပ်နေပါပြီ။',
        ]);
    });

});