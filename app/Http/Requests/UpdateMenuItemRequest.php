<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menuItemId =
            $this->route('menu_item');

        return [
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'menu_items',
                    'slug'
                )->ignore($menuItemId),
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'is_available' => [
                'sometimes',
                'boolean',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }
}