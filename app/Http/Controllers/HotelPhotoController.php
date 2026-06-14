<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdateHotelPhotoRequest;
use App\Models\Hotel;
use App\Models\Photo;
use App\Services\PhotoUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HotelPhotoController extends Controller
{
    public function __construct(
        private readonly PhotoUploadService $photoUploadService
    ) {}

    public function index(Hotel $hotel): View
    {
        $this->authorize('viewAny', [Photo::class, $hotel]);

        $photos = $hotel->photos()->get();

        return view('hotel-photos.index', compact('hotel', 'photos'));
    }

    public function store(StorePhotoRequest $request, Hotel $hotel): RedirectResponse
    {
        $this->authorize('create', [Photo::class, $hotel]);

        $validated = $request->validated();
        $photoCount = $hotel->photos()->count();

        $order = isset($validated['order'])
            ? (int) $validated['order']
            : $photoCount + 1;

        DB::transaction(function () use ($request, $hotel, $order): void {
            $hotel->photos()
                ->where('order', '>=', $order)
                ->increment('order');

            $this->photoUploadService->upload(
                $request->file('photo'),
                $hotel,
                $order
            );
        });

        return redirect()
            ->route('manage.hotels.photos.index', $hotel)
            ->with('success', 'Zdjęcie zostało dodane.');
    }

    public function update(UpdateHotelPhotoRequest $request, Hotel $hotel, Photo $photo): RedirectResponse
    {
        $this->ensurePhotoBelongsToHotel($hotel, $photo);
        $this->authorize('update', [$photo, $hotel]);

        $oldOrder = $photo->order;
        $newOrder = (int) $request->validated('order');

        if ($oldOrder !== $newOrder) {
            DB::transaction(function () use ($hotel, $photo, $oldOrder, $newOrder): void {
                if ($newOrder > $oldOrder) {
                    $hotel->photos()
                        ->where('id', '!=', $photo->id)
                        ->whereBetween('order', [$oldOrder + 1, $newOrder])
                        ->decrement('order');
                } elseif ($newOrder < $oldOrder) {
                    $hotel->photos()
                        ->where('id', '!=', $photo->id)
                        ->whereBetween('order', [$newOrder, $oldOrder - 1])
                        ->increment('order');
                }

                $photo->update(['order' => $newOrder]);
            });
        }

        return redirect()
            ->route('manage.hotels.photos.index', $hotel)
            ->with('success', 'Kolejność zdjęcia została zaktualizowana.');
    }

    public function destroy(Hotel $hotel, Photo $photo): RedirectResponse
    {
        $this->ensurePhotoBelongsToHotel($hotel, $photo);
        $this->authorize('delete', [$photo, $hotel]);

        $this->photoUploadService->delete($photo);

        return redirect()
            ->route('manage.hotels.photos.index', $hotel)
            ->with('success', 'Zdjęcie zostało usunięte.');
    }

    private function ensurePhotoBelongsToHotel(Hotel $hotel, Photo $photo): void
    {
        abort_if(
            ! $photo->imageable()->is($hotel),
            404
        );
    }
}
