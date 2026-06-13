@php
    $standardAmenities = $room->standardAmenities();
    $paidRoomAmenities = $room->optionalPaidAmenities();
@endphp

@if ($standardAmenities->isNotEmpty())
    <div class="mb-4">
        <h2 class="h6 fw-bold border-bottom pb-2 mb-3">Domyślne udogodnienia (w cenie)</h2>
        <ul class="list-group list-group-flush">
            @foreach ($standardAmenities as $roomAmenity)
                <li class="list-group-item d-flex justify-content-between px-0">
                    <span>{{ $roomAmenity->hotelAmenity->amenity->name }}</span>
                    <span class="text-success">Gratis</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

@if ($paidRoomAmenities->isNotEmpty())
    <div class="mb-4">
        <h2 class="h6 fw-bold border-bottom pb-2 mb-3">Opcjonalne płatne udogodnienia</h2>
        <ul class="list-group list-group-flush">
            @foreach ($paidRoomAmenities as $roomAmenity)
                @php
                    $selectedExtra = $booking->extraAmenities->first(
                        fn ($extra) => $extra->hotel_amenity_id === $roomAmenity->hotel_amenity_id
                    );
                @endphp
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span>
                        {{ $roomAmenity->hotelAmenity->amenity->name }}
                        @if ($selectedExtra)
                            <span class="badge bg-primary ms-1">Wybrane</span>
                        @endif
                    </span>
                    <span>
                        @if ($selectedExtra)
                            {{ number_format($selectedExtra->price, 2) }} PLN
                        @else
                            {{ number_format($roomAmenity->price, 2) }} PLN
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
