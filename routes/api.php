<?php


use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\MenuItemController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PublicMenuController;
use App\Http\Controllers\Api\V1\RestaurantTableController;
use App\Http\Controllers\Api\V1\DashboardController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Guest / Public Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    Route::post('/auth/login', [
        AuthController::class,
        'login',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Customer / Guest
    |--------------------------------------------------------------------------
    */
    Route::get('/public/categories', [
        CategoryController::class,
        'index',
    ]);

    Route::get('/public/menu-items', [
        PublicMenuController::class,
        'menuItems',
    ]);

    Route::get('/public/tables/{table_code}', [
        RestaurantTableController::class,
        'show',
    ]);

    Route::post('/public/orders', [
        OrderController::class,
        'store',
    ]);

    Route::get('/public/orders/{order_number}', [
        OrderController::class,
        'publicShow',
    ]);

    Route::get('/public/orders/track/{trackingToken}', [
        OrderController::class,
        'track',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Protected Admin / Staff
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard', [
            DashboardController::class,
            'index',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Current User
        |--------------------------------------------------------------------------
        */
        Route::get('/auth/me', [
            AuthController::class,
            'me',
        ]);

        Route::post('/auth/logout', [
            AuthController::class,
            'logout',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Category Read
        |--------------------------------------------------------------------------
        */
        Route::get('/categories', [
            CategoryController::class,
            'index',
        ]);

        Route::get('/categories/{category}', [
            CategoryController::class,
            'show',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Menu Read
        |--------------------------------------------------------------------------
        */
        Route::get('/menu-items', [
            MenuItemController::class,
            'index',
        ]);

        Route::get('/menu-items/{menu_item}', [
            MenuItemController::class,
            'show',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        | admin + staff
        |--------------------------------------------------------------------------
        */
        Route::get('/orders', [
            OrderController::class,
            'index',
        ]);

        Route::get('/orders/{order}', [
            OrderController::class,
            'show',
        ]);

        Route::patch('/orders/{order}/status', [
            OrderController::class,
            'updateStatus',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Kitchen
        |--------------------------------------------------------------------------
        */
        Route::get('/kitchen/orders', [
            OrderController::class,
            'kitchen',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Admin Only
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:admin')->group(function () {

            /*
            | Categories CRUD
            */
            Route::post('/categories', [
                CategoryController::class,
                'store',
            ]);

            Route::put('/categories/{category}', [
                CategoryController::class,
                'update',
            ]);

            Route::delete('/categories/{category}', [
                CategoryController::class,
                'destroy',
            ]);

            /*
            | Menu CRUD
            */
            Route::post('/menu-items', [
                MenuItemController::class,
                'store',
            ]);

            Route::post('/menu-items/{menu_item}', [
                MenuItemController::class,
                'update',
            ]);

            Route::delete('/menu-items/{menu_item}', [
                MenuItemController::class,
                'destroy',
            ]);

            /*
            | Restaurant Tables
            */
            Route::apiResource(
                'tables',
                RestaurantTableController::class
            )->except([
                'show',
            ]);

            /*
            | QR
            */
            Route::get('/tables/{restaurant_table}/qr', [
                RestaurantTableController::class,
                'qr',
            ]);
        });
    });
});
