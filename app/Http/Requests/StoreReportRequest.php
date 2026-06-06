<?php

namespace App\Http\Requests;

use App\Enums\ReportTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', Rule::enum(ReportTitle::class)],
            'reason' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Wybierz typ zgłoszenia.',
            'reason.min' => 'Opis musi mieć co najmniej 10 znaków.',
        ];
    }
}
