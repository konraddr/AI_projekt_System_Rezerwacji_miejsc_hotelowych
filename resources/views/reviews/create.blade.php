@extends('layouts.app')

@section('title', 'Dodaj opinię — '.$hotel->name)

@section('content')
    <div class="container py-2">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hotels.show', $hotel) }}">{{ $hotel->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Nowa opinia</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h4 mb-0">Dodaj opinię: {{ $hotel->name }}</h1>
                    </div>
                    <div class="card-body">
                        @if ($existingReview)
                            <div class="alert alert-info">Masz już opinię o tym hotelu.
                                <a href="{{ route('manage.hotels.reviews.edit', [$hotel, $existingReview]) }}">Przejdź do edycji</a>.
                            </div>
                        @endif

                        <form action="{{ route('manage.hotels.reviews.store', $hotel) }}" method="POST">
                            @csrf

                            @unless ($existingReview)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold" for="booking_id">Rezerwacja</label>
                                    <select name="booking_id" id="booking_id"
                                            class="form-select @error('booking_id') is-invalid @enderror" required>
                                        <option value="" disabled @selected(old('booking_id') === null)>Wybierz…</option>
                                        @foreach ($eligibleBookings as $booking)
                                            <option value="{{ $booking->id }}" @selected((int) old('booking_id') === $booking->id)>
                                                #{{ $booking->id }} — {{ $booking->room->name }}
                                                ({{ $booking->check_in->format('d.m.Y') }}–{{ $booking->check_out->format('d.m.Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('booking_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">Opinię można dodać tylko po opłaconym i zakończonym pobycie.</div>
                                </div>
                            @endunless

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="rating">Ocena</label>
                                <select name="rating" id="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                    <option value="" disabled @selected(old('rating') === null)>Wybierz…</option>
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" @selected((int) old('rating') === $i)>{{ $i }} — {{ str_repeat('★', $i) }}</option>
                                    @endfor
                                </select>
                                @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="comment">Komentarz</label>
                                <textarea name="comment" id="comment" rows="5"
                                          class="form-control @error('comment') is-invalid @enderror"
                                          required minlength="10">{{ old('comment') }}</textarea>
                                @error('comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" @disabled($existingReview)>Opublikuj opinię</button>
                                <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-outline-secondary">Anuluj</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
