<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_code' => [
                'required',
                'string',
                'exists:restaurant_tables,table_code',
            ],

            'guest_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'guest_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.menu_item_id' => [
                'required',
                'integer',
                'exists:menu_items,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:99',
            ],

            'items.*.special_note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}