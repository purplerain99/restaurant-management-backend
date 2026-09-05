<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantTableRequest extends FormRequest
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
                'max:100',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'status' => [
                'sometimes',
                'in:available,occupied,reserved,inactive',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'စားပွဲအမည် ထည့်ရန်လိုအပ်ပါသည်။',

            'capacity.required' =>
                'လူဦးရေ ထည့်ရန်လိုအပ်ပါသည်။',

            'capacity.min' =>
                'Capacity သည် အနည်းဆုံး ၁ ဖြစ်ရပါမည်။',
        ];
    }
}