<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'short_code' => 'required|string|max:255',
            'payment_type_id' => 'required|exists:payment_types,id',  // Ensure the payment_type_id exists in the payment_types table
            'payment_method' => 'required|string|max:255',
        ];
    }
}
