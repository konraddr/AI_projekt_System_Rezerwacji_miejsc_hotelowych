<?php

namespace App\Http\Requests;

use App\Services\HotelAccessService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachHotelWorkerRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
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
            'email.required' => 'Podaj adres e-mail pracownika.',
            'email.email' => 'Podaj prawidłowy adres e-mail.',
            'email.exists' => 'Nie znaleziono użytkownika o podanym adresie e-mail.',
            'permissions.required' => 'Wybierz co najmniej jedno uprawnienie.',
            'permissions.min' => 'Wybierz co najmniej jedno uprawnienie.',
        ];
    }
}
