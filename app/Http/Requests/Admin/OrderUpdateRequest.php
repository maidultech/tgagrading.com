<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
            'custom_unit_price' => 'nullable|numeric',
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
            'admin_tracking_id' => 'nullable|string',
            'admin_tracking_note' => 'nullable',
            'customer_tracking_url' => 'nullable|string',
            'customer_tracking_note' => 'nullable',
            'payment_status' => 'required|in:0,1',
            'order_status' => 'required|in:'.collect(config('static_array.status'))->keys()->join(','),
            'shippingName' => 'required',
            'shippingAddress' => 'required',
            'shippingCity' => 'required',
            'shippingState' => 'required',
            'shippingZip' => 'required',
            'shippingCountry' => 'required',
            'shippingPhone' => 'required',
        ];
    }
}
