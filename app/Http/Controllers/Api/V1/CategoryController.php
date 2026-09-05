<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(
        Request $request
    ): JsonResponse {

        $query = Category::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search =
                $request->string('search');

            $query->where(
                'name',
                'like',
                "%{$search}%"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Active Filter
        |--------------------------------------------------------------------------
        */

        if ($request->has('is_active')) {

            $query->where(
                'is_active',
                $request->boolean('is_active')
            );
        }

        $categories = $query
            ->withCount('menuItems')
            ->orderBy('name')
            ->paginate(15);

        return response()->json([
            'success' => true,

            'data' => [
                'items' =>
                    $categories->items(),

                'pagination' => [
                    'current_page' =>
                        $categories->currentPage(),

                    'last_page' =>
                        $categories->lastPage(),

                    'per_page' =>
                        $categories->perPage(),

                    'total' =>
                        $categories->total(),

                    'from' =>
                        $categories->firstItem(),

                    'to' =>
                        $categories->lastItem(),
                ],
            ],
        ]);
    }


    public function store(
        StoreCategoryRequest $request
    ): JsonResponse {

        $category =
            Category::create(
                $request->validated()
            );

        return response()->json([
            'success' => true,

            'message' =>
                'Category ဖန်တီးပြီးပါပြီ။',

            'data' => $category,
        ], 201);
    }


    public function show(
        Category $category
    ): JsonResponse {

        $category->loadCount(
            'menuItems'
        );

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }


    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ): JsonResponse {

        $category->update(
            $request->validated()
        );

        return response()->json([
            'success' => true,

            'message' =>
                'Category ပြင်ဆင်ပြီးပါပြီ။',

            'data' =>
                $category->fresh()
                    ->loadCount('menuItems'),
        ]);
    }


    public function destroy(
        Category $category
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Prevent deleting category with menu items
        |--------------------------------------------------------------------------
        */

        if (
            $category
                ->menuItems()
                ->exists()
        ) {

            return response()->json([
                'success' => false,

                'message' =>
                    'Menu Item များရှိနေသေးသောကြောင့် ' .
                    'ဒီ Category ကို ဖျက်၍မရပါ။',
            ], 422);
        }


        $category->delete();

        return response()->json([
            'success' => true,

            'message' =>
                'Category ကို ဖျက်ပြီးပါပြီ။',
        ]);
    }
}