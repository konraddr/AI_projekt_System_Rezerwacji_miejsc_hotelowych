<div class="mb-4">
    <h2 class="h6 fw-semibold mb-2">Zdjęcia hotelu</h2>
    @if (isset($hotel))
        <a href="{{ route('manage.hotels.photos.index', $hotel) }}" class="btn btn-outline-info">
            Przejdź do galerii zdjęć
        </a>
    @else
        <button type="button" class="btn btn-outline-info" disabled
                title="Zapisz hotel, aby przejść do zarządzania zdjęciami">
            Przejdź do galerii zdjęć
        </button>
        <p class="text-muted small mt-2 mb-0">Zdjęcia dodasz po pierwszym zapisaniu hotelu (przycisk będzie aktywny na stronie edycji).</p>
    @endif
</div>
