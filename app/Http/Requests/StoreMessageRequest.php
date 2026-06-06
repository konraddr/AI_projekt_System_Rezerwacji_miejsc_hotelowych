<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'content' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_id.required' => 'Wybierz odbiorcę wiadomości.',
            'receiver_id.exists' => 'Wybrany odbiorca nie istnieje.',
            'content.required' => 'Treść wiadomości jest wymagana.',
            'content.max' => 'Wiadomość może mieć maksymalnie 2000 znaków.',
        ];
    }
}
