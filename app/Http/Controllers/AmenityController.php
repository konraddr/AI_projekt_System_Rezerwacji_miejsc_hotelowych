<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAmenityRequest;
use App\Http\Requests\UpdateAmenityRequest;
use App\Models\Amenity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AmenityController extends Controller
{
    public function index(): View
    {
        $amenities = Amenity::query()
            ->withCount('hotels')
            ->orderBy('name')
            ->get();

        return view('amenities.index', compact('amenities'));
    }

    public function create(): View
    {
        return view('amenities.create');
    }

    public function store(StoreAmenityRequest $request): RedirectResponse
    {
        Amenity::create($request->validated());

        return redirect()
            ->route('manage.amenities.index')
            ->with('success', 'Udogodnienie zostało dodane.');
    }

    public function edit(Amenity $amenity): View
    {
        return view('amenities.edit', compact('amenity'));
    }

    public function update(UpdateAmenityRequest $request, Amenity $amenity): RedirectResponse
    {
        $amenity->update($request->validated());

        return redirect()
            ->route('manage.amenities.index')
            ->with('success', 'Udogodnienie zostało zaktualizowane.');
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        if ($amenity->hotels()->exists()) {
            return redirect()
                ->route('manage.amenities.index')
                ->with('error', 'Nie można usunąć udogodnienia przypisanego do hotelu.');
        }

        $amenity->delete();

        return redirect()
            ->route('manage.amenities.index')
            ->with('success', 'Udogodnienie zostało usunięte.');
    }
}
