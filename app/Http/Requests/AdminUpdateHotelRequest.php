<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission(\App\Enums\UserPermission::Administrator) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ... (new StoreHotelRequest())->rules(),
            'owner_id' => ['required', 'integer', Rule::exists('users', 'id')],
        ];
    }
}
