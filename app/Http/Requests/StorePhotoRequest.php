<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StorePhotoRequest extends FormRequest
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
        $maxSizeKb = (int) config('photos.max_size_kb', 5120);
        $mimes = config('photos.allowed_mimes', ['jpeg', 'jpg', 'png']);
        $imageable = $this->route('room') ?? $this->route('hotel');
        $photoCount = $imageable->photos()->count();

        return [
            'photo' => [
                'required',
                File::image()
                    ->types($mimes)
                    ->max($maxSizeKb),
            ],
            'order' => ['nullable', 'integer', 'min:1', 'max:'.($photoCount + 1)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'Wybierz plik ze zdjęciem.',
            'photo.image' => 'Plik musi być obrazem.',
            'photo.max' => 'Zdjęcie nie może być większe niż :max kilobajtów.',
            'order.min' => 'Kolejność musi być co najmniej 1.',
            'order.max' => 'Kolejność nie może być większa niż :max.',
        ];
    }
}
