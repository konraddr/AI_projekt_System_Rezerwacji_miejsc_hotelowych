<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->user()->id],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Imię jest wymagane.',
            'email.required' => 'Adres e-mail jest wymagany.',
            'email.unique' => 'Ten adres e-mail jest już zajęty.',
            'current_password.required_with' => 'Podaj aktualne hasło, aby je zmienić.',
            'current_password.current_password' => 'Aktualne hasło jest nieprawidłowe.',
            'password.confirmed' => 'Nowe hasła nie są identyczne.',
        ];
    }
}
