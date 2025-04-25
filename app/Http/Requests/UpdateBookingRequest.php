<?php

namespace App\Http\Requests;

use App\Rules\PromoCodeRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Since we're using sanctum middleware in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guest_name' => 'sometimes|required|string|max:255',
            'check_in_date' => 'sometimes|required|date|after:today',
            'check_out_date' => 'sometimes|required|date|after:check_in_date',
            'status' => 'sometimes|required|in:pending,confirmed,cancelled',
            'promo_code' => ['nullable', 'string', 'max:50', new PromoCodeRule],
        ];
    }
}
