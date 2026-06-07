<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachHotelWorkerRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Wybierz użytkownika do dodania.',
            'user_id.exists' => 'Wybrany użytkownik nie istnieje.',
        ];
    }
}
