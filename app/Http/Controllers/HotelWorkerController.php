<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachHotelWorkerRequest;
use App\Models\Hotel;
use App\Models\User;
use App\Services\HotelAccessService;
use App\Services\HotelWorkerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class HotelWorkerController extends Controller
{
    public function __construct(
        private readonly HotelAccessService $hotelAccess,
        private readonly HotelWorkerService $hotelWorkerService
    ) {
        $this->middleware('auth');
    }

    public function index(Hotel $hotel): View
    {
        $this->hotelAccess->authorizeHotelAccess(auth()->user(), $hotel);

        $workers = $this->hotelWorkerService->workersForHotel($hotel);
        $assignableUsers = $this->hotelWorkerService->assignableUsers($hotel);

        return view('hotel-workers.index', compact('hotel', 'workers', 'assignableUsers'));
    }

    public function store(AttachHotelWorkerRequest $request, Hotel $hotel): RedirectResponse
    {
        $this->hotelAccess->authorizeHotelAccess($request->user(), $hotel);

        $worker = User::query()->findOrFail($request->validated('user_id'));

        try {
            $this->hotelWorkerService->attachWorker($hotel, $worker);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Pracownik został dodany.');
    }

    public function destroy(Hotel $hotel, User $user): RedirectResponse
    {
        $this->hotelAccess->authorizeHotelAccess(auth()->user(), $hotel);

        try {
            $this->hotelWorkerService->detachWorker($hotel, $user);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Pracownik został usunięty z hotelu.');
    }
}
