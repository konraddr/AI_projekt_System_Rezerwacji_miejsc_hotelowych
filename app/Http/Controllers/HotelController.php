<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hotels = Hotel::with('amenities')->get();


        return view('hotels.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $amenities = \App\Models\Amenity::all();

        return view('hotels.create', compact('amenities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'required|string',
            'amenities' => 'nullable|array',
        ]);

        $hotel = Hotel::create([
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'latitude' => $request->input('latitude', 52.069),
            'longitude' => $request->input('longitude', 19.480),
        ]);

        if (!empty($validated['amenities'])) {
            foreach ($validated['amenities'] as $amenityId) {
                $price = $request->input('amenity_prices.' . $amenityId);
                $price = $price !== null ? (float)$price : 0;

                $hotel->amenities()->attach($amenityId, ['price' => $price]);
            }
        }
        return redirect()->route('hotels.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Hotel $hotel)
    {
        //
        $hotel->load(['rooms', 'amenities']);

        return view('hotels.show', compact('hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hotel $hotel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        //
    }
}
