<?php

namespace App\Http\Requests\Api\v1\Order;

use App\Rules\SufficientGoldBalance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type'        => ['required', 'string', Rule::in([
                \App\Models\Order::TYPE_BUY,
                \App\Models\Order::TYPE_SELL,
            ])],
            'amount'      => [
                'required',
                'numeric',
                'min:0.001',
                new SufficientGoldBalance($this->input('amount')),
            ],
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ];
    }
}
