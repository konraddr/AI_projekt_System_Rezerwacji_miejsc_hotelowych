<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdateHotelPhotoRequest;
use App\Models\Hotel;
use App\Models\Photo;
use App\Services\PhotoUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HotelPhotoController extends Controller
{
    public function __construct(
        private readonly PhotoUploadService $photoUploadService
    ) {}

    public function index(Hotel $hotel): View
    {
        $this->authorize('viewAny', Photo::class);

        $photos = $hotel->photos()->get();

        return view('hotel-photos.index', compact('hotel', 'photos'));
    }

    public function store(StorePhotoRequest $request, Hotel $hotel): RedirectResponse
    {
        $this->authorize('create', Photo::class);

        $validated = $request->validated();
        $nextOrder = $hotel->photos()->max('order');

        $order = isset($validated['order'])
            ? (int) $validated['order']
            : (int) $nextOrder + 1;

        $this->photoUploadService->upload(
            $request->file('photo'),
            $hotel,
            $order
        );

        return redirect()
            ->route('manage.hotels.photos.index', $hotel)
            ->with('success', 'Zdjęcie zostało dodane.');
    }

    public function update(UpdateHotelPhotoRequest $request, Hotel $hotel, Photo $photo): RedirectResponse
    {
        $this->ensurePhotoBelongsToHotel($hotel, $photo);
        $this->authorize('update', $photo);

        $photo->update([
            'order' => (int) $request->validated('order'),
        ]);

        return redirect()
            ->route('manage.hotels.photos.index', $hotel)
            ->with('success', 'Kolejność zdjęcia została zaktualizowana.');
    }

    public function destroy(Hotel $hotel, Photo $photo): RedirectResponse
    {
        $this->ensurePhotoBelongsToHotel($hotel, $photo);
        $this->authorize('delete', $photo);

        $this->photoUploadService->delete($photo);

        return redirect()
            ->route('manage.hotels.photos.index', $hotel)
            ->with('success', 'Zdjęcie zostało usunięte.');
    }

    private function ensurePhotoBelongsToHotel(Hotel $hotel, Photo $photo): void
    {
        abort_if(
            $photo->imageable_type !== $hotel->getMorphClass() || $photo->imageable_id !== $hotel->id,
            404
        );
    }
}
