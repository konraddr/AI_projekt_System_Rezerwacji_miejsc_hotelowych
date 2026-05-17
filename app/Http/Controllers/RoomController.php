<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Hotel $hotel)
    {
        //
        $amenities = $hotel->amenities;
        return view('hotels.create', compact('hotel', 'amenities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Hotel $hotel)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
        ]);

        $room = $hotel->rooms()->create([
            'name' => $validated['name'],
            'capacity' => $validated['capacity'],
            'price_per_night' => $validated['price_per_night'],
            'quantity' => $validated['quantity'],
        ]);


        if (!empty($validated['amenities'])) {
            foreach ($validated['amenities'] as $amenityId) {
                $price = $request->input('amenity_prices.' . $amenityId);
                $price = $price !== null ? (float)$price : 0;

                $room->amenities()->attach($amenityId, ['price' => $price]);
            }
        }

        return redirect()->route('hotels.show', $hotel);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        //
    }
}
