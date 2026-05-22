<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\HotelAmenity;
use App\Models\Room;
use App\Models\RoomAmenity;

class AmenityInheritanceService
{
    /**
     * @param  array<int, float|int|string>  $amenityPrices  amenity_id => price
     */
    public function syncHotelAmenities(Hotel $hotel, array $amenityPrices): void
    {
        $syncData = [];

        foreach ($amenityPrices as $amenityId => $price) {
            $syncData[$amenityId] = ['price' => (float) $price];
        }

        $hotel->amenities()->sync($syncData);
    }

    /**
     * Pokój dziedziczy tylko udogodnienia przypisane do hotelu w hotel_amenity.
     *
     * @param  array<int, float|int|string>  $amenityPrices  amenity_id => price (0 = gratis)
     */
    public function syncRoomAmenities(Room $room, array $amenityPrices): void
    {
        $room->roomAmenities()->delete();

        $hotelAmenities = HotelAmenity::query()
            ->where('hotel_id', $room->hotel_id)
            ->whereIn('amenity_id', array_keys($amenityPrices))
            ->get()
            ->keyBy('amenity_id');

        foreach ($amenityPrices as $amenityId => $price) {
            $hotelAmenity = $hotelAmenities->get($amenityId);

            if ($hotelAmenity === null) {
                continue;
            }

            RoomAmenity::create([
                'room_id' => $room->id,
                'hotel_amenity_id' => $hotelAmenity->id,
                'price' => (float) $price,
            ]);
        }
    }

    /**
     * @param  array<int|string>|null  $amenityIds
     * @param  array<int|string, mixed>  $priceInputs
     * @return array<int, float>
     */
    public static function parseAmenityPrices(?array $amenityIds, array $priceInputs): array
    {
        $amenityPrices = [];

        foreach ($amenityIds ?? [] as $amenityId) {
            $amenityPrices[(int) $amenityId] = isset($priceInputs[$amenityId])
                ? (float) $priceInputs[$amenityId]
                : 0.0;
        }

        return $amenityPrices;
    }
}
