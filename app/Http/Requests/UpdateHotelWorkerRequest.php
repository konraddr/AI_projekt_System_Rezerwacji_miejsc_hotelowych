<?php

namespace App\Http\Requests;

use App\Services\HotelAccessService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $hotel = $this->route('hotel');

        return $hotel !== null
            && app(HotelAccessService::class)->userCanManageWorkerRoles($this->user(), $hotel);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', Rule::in($this->assignablePermissions())],
        ];
    }

    /**
     * @return list<string>
     */
    private function assignablePermissions(): array
    {
        $hotel = $this->route('hotel');

        if ($hotel === null) {
            return [];
        }

        return app(HotelAccessService::class)->assignableWorkerPermissionsFor($this->user(), $hotel);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'permissions.required' => 'Wybierz co najmniej jedno uprawnienie.',
            'permissions.min' => 'Wybierz co najmniej jedno uprawnienie.',
        ];
    }
}
