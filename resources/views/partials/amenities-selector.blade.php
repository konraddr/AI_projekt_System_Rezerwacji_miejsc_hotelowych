@php
    $selectedAmenities = $selectedAmenities ?? [];
    $amenityPrices = $amenityPrices ?? [];
@endphp

<div class="list-group mb-3">
    @forelse ($amenities as $amenity)
        @php
            $isChecked = in_array($amenity->id, $selectedAmenities, false)
                || in_array((string) $amenity->id, $selectedAmenities, true);
            $inheritedPrice = isset($amenity->pivot) ? (float) $amenity->pivot->price : 0.0;
            $priceValue = old(
                'amenity_prices.'.$amenity->id,
                $amenityPrices[$amenity->id] ?? $inheritedPrice
            );
        @endphp
        <div class="list-group-item">
            <div class="row align-items-center g-2">
                <div class="col-12 col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]"
                               value="{{ $amenity->id }}" id="amenity_{{ $amenity->id }}"
                               @checked($isChecked)>
                        <label class="form-check-label fw-semibold" for="amenity_{{ $amenity->id }}">
                            {{ $amenity->name }}
                        </label>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Cena</span>
                        <input type="number" name="amenity_prices[{{ $amenity->id }}]"
                               class="form-control text-end @error('amenity_prices.'.$amenity->id) is-invalid @enderror"
                               value="{{ $priceValue }}" min="0" step="0.01" placeholder="0 = gratis">
                        <span class="input-group-text">PLN</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-warning mb-0">Brak dostępnych udogodnień.</div>
    @endforelse
</div>
