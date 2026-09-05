<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateOrderStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * Customer creates order.
     */
    public function store(
        StoreOrderRequest $request
    ): JsonResponse {

        $order =
            $this->orderService->createOrder(
                $request->validated()
            );

        // return response()->json([
        //     'success' => true,

        //     'message' =>
        //         'Order တင်ပြီးပါပြီ။',

        //     'data' => $order,
        // ], 201);
        return response()->json([
            'success' => true,

            'message' =>
            'Order တင်ပြီးပါပြီ။',

            'data' => [
                'order_number' =>
                $order->order_number,

                'tracking_token' =>
                $order->tracking_token,

                'status' =>
                $order->status,

                'grand_total' =>
                $order->grand_total,
            ],
        ], 201);
    }

    /**
     * Customer checks order.
     */
    public function publicShow(string $order_number)
    {
        $order = Order::query()
            ->with([
                'restaurantTable:id,name,table_code',
                'orderItems',
            ])
            ->where('order_number', $order_number)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tracking information မတွေ့ပါ။',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    public function index(
        Request $request
    ): JsonResponse {

        $query = Order::query()
            ->with([
                'restaurantTable',
                'orderItems',
            ]);



        if (
            $request->boolean('kitchen')
        ) {
            $query->whereIn(
                'status',
                [
                    'pending',
                    'confirmed',
                    'preparing',
                    'ready',
                ]
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')
            );
        }

        if ($request->filled('search')) {

            $search =
                $request->string('search');

            $query->where(function ($q)
            use ($search) {

                $q->where(
                    'order_number',
                    'like',
                    "%{$search}%"
                );

                $q->orWhereHas(
                    'restaurantTable',
                    function ($tableQuery)
                    use ($search) {

                        $tableQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
            });
        }

        $orders = $query
            ->latest('id')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order
    ): JsonResponse {

        $oldStatus =
            $order->status;

        $newStatus =
            $request->validated()['status'];


        DB::transaction(
            function () use (
                $order,
                $newStatus
            ) {

                $order->update([
                    'status' =>
                    $newStatus,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Free table
                |--------------------------------------------------------------------------
                */

                if (
                    in_array(
                        $newStatus,
                        [
                            'completed',
                            'cancelled',
                        ],
                        true
                    )
                ) {

                    $order
                        ->restaurantTable
                        ->update([
                            'status' =>
                            'available',
                        ]);
                }
            }
        );


        $order->load([
            'restaurantTable',
            'orderItems',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Broadcast after DB update
        |--------------------------------------------------------------------------
        */

        event(
            new OrderStatusUpdated(
                $order,
                $oldStatus
            )
        );


        return response()->json([
            'success' => true,

            'message' =>
            'Order status ပြောင်းပြီးပါပြီ။',

            'data' => [
                'order' =>
                $order,
            ],
        ]);
    }

    /**
     * Tracking API.
     */
    public function track(
        string $trackingToken
    ): JsonResponse {

        $order = Order::query()
            ->with([
                'restaurantTable',
                'orderItems',
            ])
            ->where(
                'tracking_token',
                $trackingToken
            )
            ->first();


        if (! $order) {

            return response()->json([
                'success' => false,

                'message' =>
                'Order tracking information မတွေ့ပါ။',
            ], 404);
        }


        return response()->json([
            'success' => true,

            'data' => [
                'order_number' =>
                $order->order_number,

                'tracking_token' =>
                $order->tracking_token,

                'status' =>
                $order->status,

                'grand_total' =>
                $order->grand_total,

                'created_at' =>
                $order->created_at,

                'updated_at' =>
                $order->updated_at,

                'table' => [
                    'name' =>
                    $order
                        ->restaurantTable
                        ->name,
                ],

                'orderItems' =>
                $order
                    ->orderItems
                    ->map(
                        function (
                            $item
                        ) {

                            return [
                                'id' =>
                                $item->id,

                                'menu_item_name' =>
                                $item
                                    ->menu_item_name,

                                'quantity' =>
                                $item
                                    ->quantity,

                                'unit_price' =>
                                $item
                                    ->unit_price,

                                'subtotal' =>
                                $item
                                    ->subtotal,

                                'special_note' =>
                                $item
                                    ->special_note,
                            ];
                        }
                    )
                    ->values(),
            ],
        ]);
    }
}
