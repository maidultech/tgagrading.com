<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return auth('admin')->user()->hasPermission('admin.order.create');
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
            'user_id' => 'required|integer|exists:users,id',
            'custom_unit_price' => 'required|integer',
            'item_type' => 'required|integer|in:'.implode(',',range(1, count(config('static_array.item_type')))),
            'submission_type' => 'required|integer|in:'.implode(',',range(1, count(config('static_array.submission_type')))),
            'service_level' => 'required|exists:service_levels,id',
            'year' => 'required|array|min:1',
            'year.*' => 'required|integer|min:1900|max:2100',
            'brand' => 'required|array|min:1',
            'brand.*' => 'required|string|max:255',
            'cardNumber' => 'required|array',
            'cardNumber.*' => 'required',
            'playerName' => 'required|array|min:1',
            'playerName.*' => 'required|string|max:255',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:500',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric|integer|',
        ];
    }
}
