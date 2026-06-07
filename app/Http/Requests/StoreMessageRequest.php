<?php

namespace App\Http\Requests;

use App\Models\Hotel;
use App\Services\ChatRecipientService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var Hotel $hotel */
            $hotel = $this->route('hotel');

            $allowedReceiverIds = app(ChatRecipientService::class)
                ->receiversForHotel($hotel, $this->user())
                ->pluck('id');

            if (! $allowedReceiverIds->contains((int) $this->input('receiver_id'))) {
                $validator->errors()->add(
                    'receiver_id',
                    'Nie możesz wysłać wiadomości do tego użytkownika.'
                );
            }
        });
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
