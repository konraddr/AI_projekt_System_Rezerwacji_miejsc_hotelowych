<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateHotelPhotoRequest extends FormRequest
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
        $imageable = $this->route('room') ?? $this->route('hotel');
        $photoCount = $imageable->photos()->count();

        return [
            'order' => ['required', 'integer', 'min:1', 'max:'.$photoCount],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order.required' => 'Podaj kolejność wyświetlania.',
            'order.min' => 'Kolejność musi być co najmniej 1.',
            'order.max' => 'Kolejność nie może być większa niż liczba zdjęć w galerii (:max).',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $photo = $this->route('photo');
        $errorBag = $photo ? 'update-photo-'.$photo->id : 'default';

        throw (new ValidationException($validator))
            ->errorBag($errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
