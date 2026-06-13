<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Hotel;
use App\Services\ReviewEligibilityService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreReviewRequest extends FormRequest
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
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:2000'],
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
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

            $booking = Booking::query()->find($this->input('booking_id'));

            if ($booking === null) {
                return;
            }

            if (! app(ReviewEligibilityService::class)->isEligible($booking, $hotel, $this->user())) {
                $validator->errors()->add(
                    'booking_id',
                    'Wybrana rezerwacja nie uprawnia do wystawienia opinii o tym hotelu.'
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Wybierz ocenę od 1 do 5.',
            'comment.min' => 'Komentarz musi mieć co najmniej 10 znaków.',
            'booking_id.required' => 'Wybierz rezerwację, która uprawnia do wystawienia opinii.',
            'booking_id.exists' => 'Wybrana rezerwacja nie istnieje.',
        ];
    }
}
