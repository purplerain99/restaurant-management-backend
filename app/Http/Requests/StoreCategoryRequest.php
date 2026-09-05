<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'unique:categories,slug',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Category အမည် ထည့်ရန်လိုအပ်ပါသည်။',

            'slug.required' =>
                'Slug ထည့်ရန်လိုအပ်ပါသည်။',

            'slug.alpha_dash' =>
                'Slug တွင် စာလုံး၊ number၊ dash နှင့် underscore တို့ကိုသာ အသုံးပြုပါ။',

            'slug.unique' =>
                'ဒီ slug ကို အသုံးပြုပြီးသားဖြစ်ပါသည်။',
        ];
    }
}