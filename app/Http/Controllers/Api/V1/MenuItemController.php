<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Resources\MenuItemResource;

class MenuItemController extends Controller
{
    /**
     * List menu items
     */
    public function index(
        Request $request
    ): JsonResponse {
    
        $query = MenuItem::query()
            ->with([
                'category:id,name,slug',
            ]);
    
    
        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
    
        if (
            $request->filled('search')
        ) {
    
            $search =
                $request->string(
                    'search'
                );
    
            $query->where(function ($q)
                use ($search) {
    
                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                );
    
                $q->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );
            });
        }
    
    
        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */
    
        if (
            $request->filled('category_id')
        ) {
    
            $query->where(
                'category_id',
                $request->integer(
                    'category_id'
                )
            );
        }
    
    
        /*
        |--------------------------------------------------------------------------
        | Availability
        |--------------------------------------------------------------------------
        */
    
        if (
            $request->has(
                'is_available'
            )
        ) {
    
            $query->where(
                'is_available',
                $request->boolean(
                    'is_available'
                )
            );
        }
    
    
        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
    
        $menuItems =
            $query
                ->latest('id')
                ->paginate(
                    $request->integer(
                        'per_page',
                        12
                    )
                );
    
    
        return response()->json([
            'success' => true,
    
            'data' => [
                'items' =>
                    MenuItemResource::collection(
                        $menuItems
                    ),
    
                'pagination' => [
                    'current_page' =>
                        $menuItems->currentPage(),
    
                    'last_page' =>
                        $menuItems->lastPage(),
    
                    'per_page' =>
                        $menuItems->perPage(),
    
                    'total' =>
                        $menuItems->total(),
    
                    'from' =>
                        $menuItems->firstItem(),
    
                    'to' =>
                        $menuItems->lastItem(),
                ],
            ],
        ]);
    }


    /**
     * Create menu item
     */
    public function store(
        StoreMenuItemRequest $request
    ): JsonResponse {


        // return response()->json([
        //     'content_type' =>
        //         $request->header(
        //             'Content-Type'
        //         ),
    
        //     'has_file' =>
        //         $request->hasFile('image'),
    
        //     'file' =>
        //         $request->file('image'),
    
        //     'all_files' =>
        //         $request->allFiles(),
    
        //     'all' =>
        //         $request->all(),
        // ]);

        $data =
            $request->validated();


        /*
        |--------------------------------------------------------------------------
        | Upload Image
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile('image')
        ) {

            $data['image'] =
                $request
                    ->file('image')
                    ->store(
                        'menu-items',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $menuItem =
            MenuItem::create(
                $data
            );


        $menuItem->load(
            'category'
        );


        return response()->json([
            'success' => true,

            'message' =>
                'create menu successfully',

            'data' =>
                $menuItem,
        ], 201);
    }


    /**
     * Show
     */
    public function show(
        MenuItem $menuItem
    ): JsonResponse {

        $menuItem->load(
            'category'
        );

        return response()->json([
            'success' => true,
            'data' => $menuItem,
        ]);
    }


    /**
     * Update
     */
    public function update(
        UpdateMenuItemRequest $request,
        MenuItem $menuItem
    ): JsonResponse {

        $data =
            $request->validated();


        /*
        |--------------------------------------------------------------------------
        | Replace Image
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile('image')
        ) {

            /*
             * Delete old image first
             */
            if (
                $menuItem->image &&
                Storage::disk('public')
                    ->exists(
                        $menuItem->image
                    )
            ) {

                Storage::disk('public')
                    ->delete(
                        $menuItem->image
                    );
            }


            /*
             * Store new image
             */
            $data['image'] =
                $request
                    ->file('image')
                    ->store(
                        'menu-items',
                        'public'
                    );
        }


        $menuItem->update(
            $data
        );


        $menuItem->load(
            'category'
        );


        return response()->json([
            'success' => true,

            'message' =>
                'Menu Item ပြင်ဆင်ပြီးပါပြီ။',

            'data' =>
                $menuItem->fresh(
                    'category'
                ),
        ]);
    }


    /**
     * Delete
     */
    public function destroy(
        MenuItem $menuItem
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Delete Image
        |--------------------------------------------------------------------------
        */

        if (
            $menuItem->image &&
            Storage::disk('public')
                ->exists(
                    $menuItem->image
                )
        ) {

            Storage::disk('public')
                ->delete(
                    $menuItem->image
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Soft Delete
        |--------------------------------------------------------------------------
        */

        $menuItem->delete();


        return response()->json([
            'success' => true,

            'message' =>
                'Menu Item ကို ဖျက်ပြီးပါပြီ။',
        ]);
    }
}