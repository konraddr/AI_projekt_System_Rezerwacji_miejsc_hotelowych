<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'extra_amenities' => ['nullable', 'array'],
            'extra_amenities.*' => ['integer', 'exists:room_amenities,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'check_in.after_or_equal' => 'Data przyjazdu nie może być z przeszłości.',
            'check_out.after' => 'Data wyjazdu musi być późniejsza niż data przyjazdu.',
        ];
    }
}
