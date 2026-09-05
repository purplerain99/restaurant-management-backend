<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuItemResource;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicMenuController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
            ]);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }


    public function menuItems(Request $request)
    {
        $query = MenuItem::query()
            ->with('category')
            ->where('is_available', true);

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->integer('category_id')
            );
        }

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere(
                        'description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        return MenuItemResource::collection(
            $query
                ->latest()
                ->paginate(
                    $request->integer(
                        'per_page',
                        12
                    )
                )
        );
    }
}
