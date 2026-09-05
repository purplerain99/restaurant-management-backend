<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;

class PublicTableController extends Controller
{
    public function show(
        string $table_code
    ): JsonResponse {

        $table = RestaurantTable::where(
            'table_code',
            $table_code
        )->first();

        if (! $table) {
            return response()->json([
                'success' => false,
                'message' =>
                    'စားပွဲကို ရှာမတွေ့ပါ။',
            ], 404);
        }

        if ($table->status === 'inactive') {
            return response()->json([
                'success' => false,
                'message' =>
                    'ဤစားပွဲကို လက်ရှိအသုံးပြု၍မရပါ။',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $table->id,
                'name' => $table->name,
                'table_code' => $table->table_code,
                'capacity' => $table->capacity,
                'status' => $table->status,
            ],
        ]);
    }
}