<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\View\View;

class AdminHotelController extends Controller
{
    public function index(): View
    {
        $hotels = Hotel::query()
            ->withCount(['rooms', 'amenities'])
            ->latest()
            ->paginate(20);

        return view('admin.hotels.index', compact('hotels'));
    }
}
