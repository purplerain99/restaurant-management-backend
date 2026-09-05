<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
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
                'unique:menu_items,slug',
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

    public function messages(): array
    {
        return [
            'category_id.required' =>
                'Category ရွေးရန်လိုအပ်ပါသည်။',

            'category_id.exists' =>
                'ရွေးချယ်ထားသော Category မရှိပါ။',

            'name.required' =>
                'Menu Item အမည်ထည့်ရန်လိုအပ်ပါသည်။',

            'price.required' =>
                'ဈေးနှုန်းထည့်ရန်လိုအပ်ပါသည်။',

            'price.numeric' =>
                'ဈေးနှုန်းသည် number ဖြစ်ရပါမည်။',

            'image.image' =>
                'Image file မဟုတ်ပါ။',

            'image.mimes' =>
                'JPG, JPEG, PNG, WEBP တို့ကိုသာ အသုံးပြုနိုင်ပါသည်။',

            'image.max' =>
                'Image size သည် 5MB ထက် မပိုရပါ။',
        ];
    }
}