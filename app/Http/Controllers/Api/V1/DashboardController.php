<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Today's Orders
        |--------------------------------------------------------------------------
        */
        $todayOrders = Order::query()
            ->whereDate('created_at', $today)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Today's Sales
        |--------------------------------------------------------------------------
        | Cancelled orders မထည့်ပါ
        |--------------------------------------------------------------------------
        */
        $todaySales = Order::query()
            ->whereDate('created_at', $today)
            ->where('status', '=', 'completed')
            ->sum('grand_total');

        /*
        |--------------------------------------------------------------------------
        | Pending Orders
        |--------------------------------------------------------------------------
        | Pending order count ကို today's orders အဖြစ်တွက်ထားပါတယ်။
        |--------------------------------------------------------------------------
        */
        $pendingOrders = Order::query()
            ->whereDate('created_at', $today)
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'success' => true,

            'data' => [
                'today' => [
                    'orders_count' => $todayOrders,

                    'sales' => number_format(
                        (float) $todaySales,
                        2,
                        '.',
                        ''
                    ),

                    'pending_orders' => $pendingOrders,
                ],
            ],
        ]);
    }
}
