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
        $this->hotelAccess->authorizeWorkerRoleManagement(auth()->user(), $hotel);

        $workers = $this->hotelWorkerService->workersForHotel($hotel);
        $accessOptions = $this->accessOptionsForActor(auth()->user(), $hotel);

        $ownerLinks = $this->hotelAccess->ownerPanelLinks(auth()->user(), $hotel);

        return view('hotel-workers.index', compact('hotel', 'workers', 'accessOptions', 'ownerLinks'));
    }

    public function store(AttachHotelWorkerRequest $request, Hotel $hotel): RedirectResponse
    {
        $this->hotelAccess->authorizeWorkerRoleManagement($request->user(), $hotel);

        try {
            $this->hotelWorkerService->attachWorkerByEmail(
                $hotel,
                $request->validated('email'),
                $request->validated('permissions'),
                $request->user()
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
        $this->hotelAccess->authorizeWorkerRoleManagement($request->user(), $hotel);

        try {
            $this->hotelWorkerService->updateWorkerPermissions(
                $hotel,
                $user,
                $request->validated('permissions'),
                $request->user()
            );
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Uprawnienia pracownika zostały zaktualizowane.');
    }

    public function destroy(Hotel $hotel, User $user): RedirectResponse
    {
        $this->hotelAccess->authorizeWorkerRoleManagement(auth()->user(), $hotel);

        try {
            $this->hotelWorkerService->detachWorker($hotel, $user, auth()->user());
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Pracownik został usunięty z hotelu.');
    }

    /**
     * @return list<HotelWorkerAccess>
     */
    private function accessOptionsForActor(User $actor, Hotel $hotel): array
    {
        return array_map(
            fn (string $permission) => HotelWorkerAccess::from($permission),
            $this->hotelAccess->assignableWorkerPermissionsFor($actor, $hotel)
        );
    }
}
