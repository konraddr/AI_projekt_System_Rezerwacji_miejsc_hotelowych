@php
    /** @var array<int, \App\Enums\HotelWorkerAccess> $accessOptions */
    $selected = collect(old('permissions', $selected ?? []))->map(fn ($value) => (string) $value)->all();
@endphp

<div class="row g-2">
    @foreach ($accessOptions as $access)
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input @error('permissions') is-invalid @enderror @error('permissions.*') is-invalid @enderror"
                       type="checkbox"
                       name="permissions[]"
                       id="{{ $inputIdPrefix ?? 'perm' }}-{{ $access->value }}"
                       value="{{ $access->value }}"
                       @checked(in_array($access->value, $selected, true))>
                <label class="form-check-label" for="{{ $inputIdPrefix ?? 'perm' }}-{{ $access->value }}">
                    {{ $access->label() }}
                </label>
            </div>
        </div>
    @endforeach
</div>
@error('permissions')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
@error('permissions.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
