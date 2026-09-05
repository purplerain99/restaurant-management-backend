<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'confirmed',
                    'preparing',
                    'ready',
                    'served',
                    'completed',
                    'cancelled',
                ]),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            /** @var Order|null $order */
            $order = $this->route('order');

            if (! $order) {
                return;
            }

            $newStatus =
                $this->input('status');

            if (
                ! in_array(
                    $newStatus,
                    $order->allowedNextStatuses(),
                    true
                )
            ) {

                $validator->errors()->add(
                    'status',
                    sprintf(
                        '%s မှ %s သို့ ပြောင်း၍မရပါ။',
                        $order->status,
                        $newStatus
                    )
                );
            }
        });
    }
}