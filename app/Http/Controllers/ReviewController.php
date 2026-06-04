<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Hotel;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Hotel $hotel): View
    {
        $this->authorize('create', Review::class);

        $existingReview = Review::query()
            ->where('hotel_id', $hotel->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('reviews.create', compact('hotel', 'existingReview'));
    }

    public function store(StoreReviewRequest $request, Hotel $hotel): RedirectResponse
    {
        $this->authorize('create', Review::class);

        $alreadyExists = Review::query()
            ->where('hotel_id', $hotel->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($alreadyExists) {
            return redirect()
                ->route('manage.hotels.reviews.edit', [$hotel, Review::query()
                    ->where('hotel_id', $hotel->id)
                    ->where('user_id', $request->user()->id)
                    ->firstOrFail()])
                ->with('info', 'Masz już opinię o tym hotelu — możesz ją edytować.');
        }

        Review::create([
            'hotel_id' => $hotel->id,
            'user_id' => $request->user()->id,
            'booking_id' => $request->validated('booking_id'),
            'rating' => (int) $request->validated('rating'),
            'comment' => $request->validated('comment'),
        ]);

        return redirect()
            ->route('hotels.show', $hotel)
            ->with('success', 'Opinia została dodana.');
    }

    public function edit(Hotel $hotel, Review $review): View
    {
        $this->ensureReviewBelongsToHotel($hotel, $review);
        $this->authorize('update', $review);

        return view('reviews.edit', compact('hotel', 'review'));
    }

    public function update(UpdateReviewRequest $request, Hotel $hotel, Review $review): RedirectResponse
    {
        $this->ensureReviewBelongsToHotel($hotel, $review);
        $this->authorize('update', $review);

        $review->update($request->validated());

        return redirect()
            ->route('hotels.show', $hotel)
            ->with('success', 'Opinia została zaktualizowana.');
    }

    public function destroy(Hotel $hotel, Review $review): RedirectResponse
    {
        $this->ensureReviewBelongsToHotel($hotel, $review);
        $this->authorize('delete', $review);

        $review->delete();

        return redirect()
            ->route('hotels.show', $hotel)
            ->with('success', 'Opinia została usunięta.');
    }

    private function ensureReviewBelongsToHotel(Hotel $hotel, Review $review): void
    {
        abort_if($review->hotel_id !== $hotel->id, 404);
    }
}
