<div class="mb-4">
    <label class="form-label fw-semibold">Lokalizacja na mapie</label>
    <p class="text-muted small mb-2">
        Kliknij mapę, aby ustawić pinezkę. Miasto i adres uzupełnią się automatycznie —
        możesz je poprawić ręcznie, wtedy pinezka pozostanie bez zmian.
    </p>
    <p id="map-geocode-status" class="small text-muted mb-2"></p>

    <div id="hotel-map"
         class="hotel-map-container rounded border overflow-hidden"
         data-default-lat="{{ $latitude ?? 52.069 }}"
         data-default-lng="{{ $longitude ?? 19.480 }}">
    </div>

    <input type="hidden" name="latitude" value="{{ old('latitude', $latitude ?? 52.069) }}">
    <input type="hidden" name="longitude" value="{{ old('longitude', $longitude ?? 19.480) }}">
</div>
