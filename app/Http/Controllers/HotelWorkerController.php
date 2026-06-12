<?php

namespace App\Http\Controllers;

use App\Enums\HotelWorkerAccess;
use App\Http\Requests\AttachHotelWorkerRequest;
use App\Http\Requests\UpdateHotelWorkerRequest;
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
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Workers);

        $workers = $this->hotelWorkerService->workersForHotel($hotel);
        $accessOptions = HotelWorkerAccess::cases();

        return view('hotel-workers.index', compact('hotel', 'workers', 'accessOptions'));
    }

    public function store(AttachHotelWorkerRequest $request, Hotel $hotel): RedirectResponse
    {
        $this->hotelAccess->authorizeHotelCapability($request->user(), $hotel, HotelWorkerAccess::Workers);

        try {
            $this->hotelWorkerService->attachWorkerByEmail(
                $hotel,
                $request->validated('email'),
                $request->validated('permissions')
            );
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Pracownik został dodany.');
    }

    public function update(UpdateHotelWorkerRequest $request, Hotel $hotel, User $user): RedirectResponse
    {
        $this->hotelAccess->authorizeHotelCapability($request->user(), $hotel, HotelWorkerAccess::Workers);

        try {
            $this->hotelWorkerService->updateWorkerPermissions(
                $hotel,
                $user,
                $request->validated('permissions')
            );
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Uprawnienia pracownika zostały zaktualizowane.');
    }

    public function destroy(Hotel $hotel, User $user): RedirectResponse
    {
        $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Workers);

        try {
            $this->hotelWorkerService->detachWorker($hotel, $user);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Pracownik został usunięty z hotelu.');
    }
}
