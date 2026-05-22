<div class="mb-4">
    <label class="form-label fw-semibold">Lokalizacja na mapie</label>
    <p class="text-muted small mb-2">Kliknij mapę, aby ustawić współrzędne hotelu (szerokość i długość geograficzna).</p>

    <div id="hotel-map"
         class="hotel-map-container rounded border overflow-hidden"
         data-default-lat="{{ $latitude ?? 52.069 }}"
         data-default-lng="{{ $longitude ?? 19.480 }}">
    </div>

    <input type="hidden" name="latitude" value="{{ old('latitude', $latitude ?? 52.069) }}">
    <input type="hidden" name="longitude" value="{{ old('longitude', $longitude ?? 19.480) }}">
</div>
