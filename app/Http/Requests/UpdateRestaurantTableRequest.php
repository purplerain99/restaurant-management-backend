<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantTableRequest extends FormRequest
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
                'required',
                'in:available,occupied,reserved,inactive',
            ],
        ];
    }
}