<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantTableRequest;
use App\Http\Requests\UpdateRestaurantTableRequest;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
// use F9WebLtd\QrCode\Facades\QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RestaurantTableController extends Controller
{
    /**
     * Table List
     */
    public function index(Request $request): JsonResponse
    {
        $query = RestaurantTable::query();

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')
            );
        }

        if ($request->filled('search')) {

            $search = $request->string('search');

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                );

                $q->orWhere(
                    'table_code',
                    'like',
                    "%{$search}%"
                );
            });
        }

        $tables = $query
            ->latest('id')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }

    /**
     * Create Table
     */
    public function store(
        StoreRestaurantTableRequest $request
    ): JsonResponse {

        $table = RestaurantTable::create([
            'name' => $request->string('name'),

            'table_code' => $this->generateTableCode(),

            'capacity' => $request->integer('capacity'),

            'status' => $request->input(
                'status',
                'available'
            ),
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'စားပွဲ ဖန်တီးပြီးပါပြီ။',
            'data' => $table,
        ], 201);
    }

    /**
     * Show Table
     */
    public function show(
        RestaurantTable $restaurantTable
    ): JsonResponse {

        return response()->json([
            'success' => true,
            'data' => $restaurantTable,
        ]);
    }

    /**
     * Update Table
     */
    public function update(
        UpdateRestaurantTableRequest $request,
        int $id
    ): JsonResponse {
        $restaurantTable = RestaurantTable::findOrFail($id);
    
        $restaurantTable->update([
            'name' => $request->string('name')->value(),
            'table_code' => $this->generateTableCode(),
            'capacity' => $request->integer('capacity'),
            'status' => $request->input('status', 'available'),
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'စားပွဲ အချက်အလက်ပြင်ဆင်ပြီးပါပြီ။',
            'data' => $restaurantTable,
        ], 200);
    }

    /**
     * Delete Table
     */
    public function destroy(
        RestaurantTable $restaurantTable
        , int $id
    ): JsonResponse {
        $restaurantTable = RestaurantTable::findOrFail($id);
        /*
         * Order ရှိပြီးသား table ကို
         * မဖျက်စေချင်ရင် production မှာ
         * restriction ထပ်ထည့်နိုင်ပါတယ်။
         */

        $restaurantTable->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'စားပွဲကို ဖျက်ပြီးပါပြီ။',
        ]);
    }

    /**
     * Generate QR Code
     */
    public function qr(
        RestaurantTable $restaurantTable,
    ) {
        $url = config(
            'app.frontend_url',
            env(
                'FRONTEND_URL',
                'http://localhost:5173'
            )
        );
        $url .= "/t/{$restaurantTable->table_code}";

        $qr = QrCode::size(400)
            ->margin(2)
            ->generate($url);

        return response($qr)
            ->header(
                'Content-Type',
                'image/svg+xml'
            )
            ->header(
                'Content-Disposition',
                'inline; filename="' .
                $restaurantTable->table_code .
                '.svg"'
            );
    }

    /**
     * Generate random unique table code
     */
    private function generateTableCode(): string
    {
        do {
            $code =
                'TBL_' .
                Str::upper(
                    Str::random(16)
                );

        } while (
            RestaurantTable::where(
                'table_code',
                $code
            )->exists()
        );

        return $code;
    }
}