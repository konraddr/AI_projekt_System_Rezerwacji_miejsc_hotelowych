<?php

namespace App\Http\Requests;

use App\Enums\HotelWorkerAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelWorkerRequest extends FormRequest
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
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', Rule::in(HotelWorkerAccess::values())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'permissions.required' => 'Wybierz co najmniej jedno uprawnienie.',
            'permissions.min' => 'Wybierz co najmniej jedno uprawnienie.',
        ];
    }
}
