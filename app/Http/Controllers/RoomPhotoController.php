<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Http\Requests\UpdateHotelPhotoRequest;
use App\Models\Hotel;
use App\Models\Photo;
use App\Models\Room;
use App\Services\PhotoUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoomPhotoController extends Controller
{
    public function __construct(
        private readonly PhotoUploadService $photoUploadService
    ) {}

    public function index(Hotel $hotel, Room $room): View
    {
        $this->ensureRoomBelongsToHotel($hotel, $room);
        $this->authorize('viewAny', [Photo::class, $hotel]);

        $photos = $room->photos()->get();

        return view('room-photos.index', compact('hotel', 'room', 'photos'));
    }

    public function store(StorePhotoRequest $request, Hotel $hotel, Room $room): RedirectResponse
    {
        $this->ensureRoomBelongsToHotel($hotel, $room);
        $this->authorize('create', [Photo::class, $hotel]);

        $validated = $request->validated();
        $photoCount = $room->photos()->count();

        $order = isset($validated['order'])
            ? (int) $validated['order']
            : $photoCount + 1;

        DB::transaction(function () use ($request, $room, $order): void {
            $room->photos()
                ->where('order', '>=', $order)
                ->increment('order');

            $this->photoUploadService->upload(
                $request->file('photo'),
                $room,
                $order
            );
        });

        return redirect()
            ->route('manage.rooms.photos.index', [$hotel, $room])
            ->with('success', 'Zdjęcie pokoju zostało dodane.');
    }

    public function update(UpdateHotelPhotoRequest $request, Hotel $hotel, Room $room, Photo $photo): RedirectResponse
    {
        $this->ensureRoomBelongsToHotel($hotel, $room);
        $this->ensurePhotoBelongsToRoom($room, $photo);
        $this->authorize('update', [$photo, $hotel]);

        $oldOrder = $photo->order;
        $newOrder = (int) $request->validated('order');

        if ($oldOrder !== $newOrder) {
            DB::transaction(function () use ($room, $photo, $oldOrder, $newOrder): void {
                if ($newOrder > $oldOrder) {
                    $room->photos()
                        ->where('id', '!=', $photo->id)
                        ->whereBetween('order', [$oldOrder + 1, $newOrder])
                        ->decrement('order');
                } elseif ($newOrder < $oldOrder) {
                    $room->photos()
                        ->where('id', '!=', $photo->id)
                        ->whereBetween('order', [$newOrder, $oldOrder - 1])
                        ->increment('order');
                }

                $photo->update(['order' => $newOrder]);
            });
        }

        return redirect()
            ->route('manage.rooms.photos.index', [$hotel, $room])
            ->with('success', 'Kolejność zdjęcia została zaktualizowana.');
    }

    public function destroy(Hotel $hotel, Room $room, Photo $photo): RedirectResponse
    {
        $this->ensureRoomBelongsToHotel($hotel, $room);
        $this->ensurePhotoBelongsToRoom($room, $photo);
        $this->authorize('delete', [$photo, $hotel]);

        $this->photoUploadService->delete($photo);

        return redirect()
            ->route('manage.rooms.photos.index', [$hotel, $room])
            ->with('success', 'Zdjęcie pokoju zostało usunięte.');
    }

    private function ensureRoomBelongsToHotel(Hotel $hotel, Room $room): void
    {
        abort_if($room->hotel_id !== $hotel->id, 404);
    }

    private function ensurePhotoBelongsToRoom(Room $room, Photo $photo): void
    {
        abort_if(! $photo->imageable()->is($room), 404);
    }
}
