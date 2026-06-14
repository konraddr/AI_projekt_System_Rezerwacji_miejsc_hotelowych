# HotelBook — System rezerwacji miejsc hotelowych

**Dokumentacja projektu zespołowego**

|  |  |
|--|--|
| Repozytorium | https://github.com/konraddr/AI_projekt_System_Rezerwacji_miejsc_hotelowych |
| Framework | Laravel 13, PHP 8.5 |
| Baza danych | PostgreSQL 18 |
| Interfejs | Blade + Bootstrap 5 + Vite |

---

## 1. Autorzy

- Konrad Dryło  
- Maciej Bigos
- Daniel Kudła

---

## 2. Technologie

### Backend
- PHP 8.5, Laravel 13
- Eloquent ORM — relacje, enumy PHP 8.1+
- Laravel Sanctum — tokeny REST API
- Laravel Notifications — database, mail, Web Push
- Form Request — walidacja wejścia

### Baza danych
- PostgreSQL 18 (kontener Docker `pgsql`)
- pgAdmin 4 — administracja bazą (port 5050)

### Frontend
- Blade — szablony serwerowe
- Bootstrap 5 — layout, formularze, tabele
- Vite + Sass — bundling assetów
- Leaflet.js — mapa hoteli
- Vanilla JavaScript — czat (polling), mapa, badge powiadomień

### Infrastruktura
- Docker Compose (Laravel Sail)
- Serwis `init` — automatyczny `composer install`, `migrate --seed`, `storage:link`
- Serwis `scheduler` — `php artisan schedule:work` 

---

## 3. Opis systemu

HotelBook to aplikacja webowa  umożliwiająca:

- **Klientom** — przeglądanie katalogu hoteli, filtrowanie po terminie i liczbie gości, rezerwację pokoi z opcjonalnymi płatnymi udogodnieniami, opłatę (symulacja), opinie po pobycie, czat z hotelem.
- **Właścicielom** — zarządzanie hotelami, pokojami, cenami udogodnień, pracownikami, podgląd rezerwacji i anulowanie.
- **Pracownikom** — ograniczony dostęp do przypisanego hotelu (pokoje, rezerwacje, zdjęcia, czat — wg uprawnień).
- **Administratorom** — moderacja użytkowników, hoteli, opinii i zgłoszeń; słownik udogodnień.

---

## 4. Zakres prac członków zespołu

### Konrad Dryło

- Schemat bazy danych modułu noclegowego: tabele `hotels`, `rooms`, `amenities`, `hotel_amenity`, `room_amenities`.
- Pełny CRUD hoteli i pokoi — widok publiczny (katalog, strona hotelu) oraz panel zarządzania właściciela (`/manage/hotels`, `/manage/hotels/{hotel}/rooms`).
- Katalog hoteli: wyszukiwanie, filtrowanie po mieście, sortowanie, paginacja oraz filtr dostępności po datach i liczbie gości (`HotelController`, `HotelAvailabilityService`).
- Dziedziczenie cen udogodnień z hotelu na pokój (`AmenityInheritanceService`, widoki z selektorem udogodnień).
- Integracja mapy Leaflet — wybór współrzędnych przy dodawaniu/edycji hotelu, wyświetlanie pinezki na stronie publicznej (`public/js/hotel-map-form.js`, `hotel-map-show.js`).
- Globalny słownik udogodnień w panelu administratora (`AmenityController`, `/manage/amenities`).
- Konfiguracja infrastruktury: Docker Compose, PostgreSQL, pgAdmin, automatyczne `migrate --seed` przy starcie (`compose.yaml`).
- Profil użytkownika — edycja danych, zmiana hasła, usuwanie konta (`ProfileController`, `/profile`).

### Maciej Bigos

- Schemat bazy danych modułu multimediów i komunikacji: tabele `photos`, `reviews`, `reports`, `messages`.
- Zdjęcia hoteli i poki, nazwa pliku jako UUID, zapis na dysku `public`, kolumny `filename`, `file_type`, `order` (`PhotoUploadService`, `HotelPhotoController`, `RoomPhotoController`).
- CRUD opinii po pobycie oraz zgłaszanie toksycznych komentarzy (`ReviewController`, `ReviewReportController`).
- Czat właściciel–klient z odpytywaniem AJAX co 60 sekund (`MessageController`, `public/js/hotel-chat-polling.js`).
- System zgłoszeń (tickety) — tworzenie przez użytkownika, obsługa statusów (`ReportController`, `AdminReportController`).
- Panel administratora (`/manage/admin`): dashboard, CRUD użytkowników i hoteli, moderacja opinii (`is_banned`), obsługa zgłoszeń (`routes/maciej.php`, kontrolery `Admin*`).
- Polityki dostępu do zdjęć, opinii i wiadomości (`PhotoPolicy`, `ReviewPolicy`, `MessagePolicy`).

### Daniel Kudła

- Schemat bazy danych modułu rezerwacji i użytkowników: tabele `users`, `workers`, `bookings`, `extra_amenities` oraz rozszerzenia profilu i uprawnień.
- Logika rezerwacji — sprawdzanie kolizji dat względem `rooms.quantity`, tworzenie, płatność (symulacja), anulowanie (`BookingService`, `BookingController`).
- Zamrażanie cen płatnych udogodnień w tabeli `extra_amenities` w momencie rezerwacji.
- System uprawnień użytkowników — enum `UserPermission` (wartości 0–6), middleware, pracownicy hotelu z granularnymi uprawnieniami JSON (`HotelWorkerController`, `HotelAccessService`).
- Automatyczna kara za brak wpłaty — anulowanie rezerwacji i ban konta (`BookingPenaltyService`, `PenalizeUnpaidBookings`, serwis `scheduler` w Dockerze).
- Powiadomienia — zmiana statusu rezerwacji, nowe wiadomości, Web Push (`Illuminate\Notifications`, `PushSubscriptionController`).
- REST API z Laravel Sanctum — logowanie tokenem Bearer, endpointy powiadomień (`routes/api.php`, `Api\AuthController`, `Api/NotificationController`).
- Anulowanie rezerwacji z panelu hotelu przez właściciela lub uprawnionego pracownika (`HotelBookingController`).

---

## 5. Uruchomienie projektu

### 5.1. Co musisz mieć włączone

- Docker Desktop (uruchomiony i działający w tle).
- Git (do pobrania kodu).
- Linux/WSL (na Windowsie używaj terminala Ubuntu lub komend przez `wsl`).

### 5.2. Sklonuj repozytorium

```bash
git clone https://github.com/konraddr/AI_projekt_System_Rezerwacji_miejsc_hotelowych.git
cd AI_projekt_System_Rezerwacji_miejsc_hotelowych
```

### 5.3. Uruchom projekt

```bash
docker compose up -d --build
```

**Pierwszy raz** po klonie: ok. 5–10 min (build obrazu, composer, npm).  
**Kolejne razy:** `docker compose up -d` — ok. 30 sekund.

> Nie uruchamiaj jako `root`. Używaj zwykłego użytkownika WSL.

**Aplikacja:** http://localhost  
**Login testowy:** `test@example.com` / `password`

### 5.4. Codzienne użycie

```bash
docker compose up -d
docker compose down
```

### 5.5. Po aktualizacji kodu

```bash
git pull origin main
docker compose up -d --build
docker compose exec laravel.test php artisan migrate --force
```

### 5.6. Pełny reset

```bash
docker compose down -v
rm -rf vendor node_modules public/build .env storage/.docker-initialized
docker compose up -d --build
```

### 5.7. pgAdmin

| Pole | Wartość |
|------|---------|
| URL | http://localhost:5050 |
| Login | `admin@local.dev` / `admin` |
| Serwer PostgreSQL | host `pgsql`, port `5432`, baza `laravel`, user `sail`, hasło `password` |

---

## 6. Scenariusz użycia 

Hasło wszystkich kont testowych: **`password`**

### Scenariusz A — Klient rezerwuje pokój

1. Wejdź na http://localhost/hotels (konto: `client@demo.pl`).
2. Ustaw przyjazd, wyjazd, liczbę gości -> **Filtruj**.
3. Wybierz hotel -> zobacz wolne pokoje w terminie.
4. Kliknij **Rezerwuj** -> wybierz daty i opcjonalne płatne udogodnienia (checkboxy).
5. Potwierdź rezerwację -> **Opłać** (symulacja) na stronie szczegółów.
6. Po zakończonym pobycie - dodaj opinię na stronie hotelu.

### Scenariusz B — Właściciel zarządza obiektem

1. Zaloguj się jako `owner@demo.pl`.
2. Panel: **Panel hoteli** -> `/manage/hotels`.
3. Dodaj/edytuj hotel (mapa Leaflet, udogodnienia z ceną).
4. Dodaj pokój - ceny udogodnień dziedziczą się z hotelu.
5. Przejrzyj rezerwacje → ewentualnie anuluj rezerwację.
6. Zarządzaj pracownikami (`worker@demo.pl`) i ich uprawnieniami.

### Scenariusz C — Administrator

1. Zaloguj się jako `test@example.com`.
2. Panel: `/manage/admin`.
3. Moderuj opinie (ban/unban), obsłuż zgłoszenia, zarządzaj użytkownikami i hotelami.
4. Dodaj globalne udogodnienie: `/manage/amenities`.


## 7. Role i uprawnienia użytkowników

Enum `UserPermission` (`app/Enums/UserPermission.php`):

```php
enum UserPermission: int
{
    case Administrator = 0;
    case Owner = 1;
    case Worker = 2;
    case Client = 5;
    case Banned = 6;
}
```

| Wartość | Uprawnienie | Dostęp |
|---------|-------------|--------|
| 0 | Administrator | Panel `/manage/admin`, udogodnienia globalne |
| 1 | Właściciel | CRUD hoteli, pracownicy |
| 2 | Pracownik | Hotel przypisany w tabeli `workers` |
| 5 | Klient | Rezerwacje, opinie, profil |
| 6 | Zbanowany | Blokada logowania (`EnsureNotBanned`) |

Pracownik ma granularne uprawnienia w JSON (`HotelWorkerAccess`): `hotel`, `rooms`, `bookings`, `workers`, `photos`, `chat`.

---

## 8. Architektura aplikacji

```
Przeglądarka (Blade + JS)
        │
routes/web.php ────────── public, bookings, profile, manage (hotele/pokoje)
routes/maciej.php ─────── zdjęcia, opinie, czat, admin (prefix /manage)
routes/api.php ────────── REST API (Sanctum)
        │
Kontrolery → Form Request (walidacja) → Serwisy → Modele → PostgreSQL
        │
Powiadomienia (DB / mail / Web Push) · Storage (zdjęcia) · Scheduler (cron)
```

### Główne warstwy

| Warstwa | Przykładowe pliki |
|---------|-------------------|
| Kontrolery | `HotelController`, `BookingController`, `AdminReviewController` |
| Serwisy | `BookingService`, `AmenityInheritanceService`, `PhotoUploadService` |
| Modele | `Hotel`, `Room`, `Booking`, `Photo`, `Review` |
| Middleware | `EnsureNotBanned`, `EnsurePermission`, `EnsureCanManageHotels` |
| Policies | `ReviewPolicy`, `PhotoPolicy`, `MessagePolicy` |

Trasy z `routes/maciej.php` są ładowane w `AppServiceProvider` z prefiksem `/manage` i middleware `web`, `auth`.

---

## 9. Baza danych

### 9.1. Opis tabel

**Rdzeń noclegowy**

- `amenities` — słownik udogodnień
- `hotel_amenity` — cena udogodnienia w hotelu
- `room_amenities` — cena udogodnienia w pokoju (`0` = gratis)
- `extra_amenities` — zamrożona cena przy rezerwacji
- `hotels` — `name`, `city`, `address`, `latitude`, `longitude`, `owner_id`
- `rooms` — `capacity`, `price_per_night`, `quantity` (ile identycznych jednostek)
- `bookings` — kolizje dat liczone względem `quantity`

**Multimedia i społeczność**

- `photos` — klucz UUID, `imageable_id` + `imageable_type` (polimorfizm), `filename`, `file_type`, `order`
- `reviews` — `rating`, `comment`, `is_banned`, `booking_id`
- `reports` — zgłoszenia ze `status`, opcjonalnie `hotel_id`, `review_id`
- `messages` — czat per hotel

**Rezerwacje i użytkownicy**

- `users` — `permission`, `last_name`, `phone`
- `workers` — `worker_id`, `hotel_id`, `permissions` (JSON)
- `bookings` — `check_in`, `check_out`, `total_price`, `payment_status`, `status`
- `extra_amenities` — `booking_id`, `hotel_amenity_id`, `price` (zamrożona)
- `notifications`, `push_subscriptions`, `personal_access_tokens` (API)

### 9.2. Diagram ERD (dbdiagram.io)

```dbml
Enum report_status {
  pending
  resolved
  rejected
}

Enum payment_status {
  pending
  paid
  failed
}

Enum booking_status {
  active
  completed
  cancelled
}

Enum report_title {
  hotel_nie_odpowiada
  toksyczny_komentarz
  inne
}

Table hotels {
  id int [pk, increment]
  name varchar
  description text
  city varchar
  address varchar
  latitude decimal [note: 'Współrzędne geograficzne - szerokość']
  longitude decimal [note: 'Współrzędne geograficzne - długość']
  created_at timestamp
  updated_at timestamp
}

Table rooms {
  id int [pk, increment]
  hotel_id int [ref: > hotels.id]
  name varchar
  description text
  capacity int
  price_per_night decimal
  quantity int [note: 'Ile takich samych pokoi ma dany hotel']
  created_at timestamp
  updated_at timestamp
}

Table amenities {
  id int [pk, increment]
  name varchar
  icon varchar [null]
  created_at timestamp
  updated_at timestamp
}

Table hotel_amenity {
  id int [pk, increment]
  hotel_id int [ref: > hotels.id]
  amenity_id int [ref: > amenities.id]
  price decimal
}

Table room_amenities {
  id int [pk, increment]
  room_id int [ref: > rooms.id]
  hotel_amenity_id int [ref: > hotel_amenity.id]
  price decimal [note: 'Cena udogodnienia w tym pokoju (0 = darmowe)']
}

Table photos {
  id uuid [pk, note: 'Generowanie przez UUIDv4']
  imageable_id int
  imageable_type varchar
  filename varchar
  file_type varchar [note: 'Rozszerzenie np. jpg, png']
  order int [note: 'Kolejność wyświetlania zdjęć']
  created_at timestamp
  updated_at timestamp
}

Table room_photos {
  rooms_id int [ref: > rooms.id]
  photos_id int [ref: > photos.id]
}

Table hotel_photos {
  hotel_id int [ref: > hotels.id]
  photos_id int [ref: > photos.id]
}

Table reviews {
  id int [pk, increment]
  booking_id int [ref: > bookings.id]
  user_id int [ref: > users.id]
  hotel_id int [ref: > hotels.id]
  rating int
  comment text
  is_banned boolean [default: false, note: 'Ukrywanie toksycznych komentarzy']
  created_at timestamp
  updated_at timestamp
}

Table reports {
  id int [pk, increment]
  user_id int [ref: > users.id]
  title report_title [default: 'hotel_nie_odpowiada']
  reason text
  status report_status [default: 'pending']
  created_at timestamp
  updated_at timestamp
}

Table messages {
  id int [pk, increment]
  sender_id int [ref: > users.id]
  receiver_id int [ref: > users.id]
  hotel_id int [ref: > hotels.id]
  content text
  is_read boolean [default: false]
  created_at timestamp
  updated_at timestamp
}

Table users {
  id int [pk, increment]
  name varchar
  LastName varchar
  Phone varchar
  email varchar [unique]
  password varchar
  permission int [note: 'Wartości 0-6 gdzie 0 to admin, 6 to ban konta']
  created_at timestamp
  updated_at timestamp
}

Table bookings {
  id int [pk, increment]
  user_id int [ref: > users.id]
  room_id int [ref: > rooms.id]
  check_in date
  check_out date
  total_price decimal
  payment_status payment_status [default: 'pending']
  status booking_status [default: 'active']
  created_at timestamp
  updated_at timestamp
}

Table extra_amenities {
  id int [pk, increment]
  booking_id int [ref: > bookings.id]
  hotel_amenity_id int [ref: > hotel_amenity.id]
  price decimal [note: 'Zamrożona cena z momentu dokonywania rezerwacji!']
}

Table workers {
  worker_id int [ref: > users.id]
  hotel_id int [ref: > hotels.id]
}
```

---

## 10. Moduły z przykładami kodu

### 10.1. Katalog hoteli — search, filter, sort, paginacja

Plik: `app/Http/Controllers/HotelController.php`

```php
$hotels = $hotelsQuery
    ->when($request->filled('q'), function ($query) use ($request) {
        $search = '%'.$request->string('q').'%';
        $query->where(function ($builder) use ($search) {
            $builder->where('name', 'ilike', $search)
                ->orWhere('city', 'ilike', $search)
                ->orWhere('address', 'ilike', $search);
        });
    })
    ->when($request->filled('city'), fn ($q) => $q->where('city', $request->string('city')))
    ->when($request->sort === 'name_asc', fn ($q) => $q->orderBy('name'))
    ->paginate(12)
    ->withQueryString();
```

Parametry URL: `?q=kraków&city=Kraków&sort=name_asc&check_in=2026-06-01&check_out=2026-06-05&guests=2`

### 10.2. Dostępność pokoi (filtr po datach)

Plik: `app/Services/HotelAvailabilityService.php`

Hotel ma wolny pokój, gdy liczba aktywnych rezerwacji nakładających się na termin jest mniejsza niż `rooms.quantity`:

```php
$roomsQuery->whereRaw(
    '(select count(*) from bookings where bookings.room_id = rooms.id
      and bookings.status = ? and bookings.check_in < ? and bookings.check_out > ?)
     < rooms.quantity',
    [BookingStatus::Active->value, $checkOut, $checkIn]
);
```

### 10.3. Dziedziczenie cen udogodnień

Plik: `app/Services/AmenityInheritanceService.php`

```php
RoomAmenity::create([
    'room_id' => $room->id,
    'hotel_amenity_id' => $hotelAmenity->id,
    'price' => (float) $price,
]);
```

### 10.4. Rezerwacja i zamrożenie cen

Plik: `app/Services/BookingService.php`

```php
public function isRoomAvailable(Room $room, Carbon $checkIn, Carbon $checkOut): bool
{
    $overlappingCount = Booking::query()
        ->where('room_id', $room->id)
        ->where('status', BookingStatus::Active)
        ->where('check_in', '<', $checkOut)
        ->where('check_out', '>', $checkIn)
        ->count();

    return $overlappingCount < $room->quantity;
}
```

### 10.5. Kara za brak wpłaty (cron)

Plik: `app/Services/BookingPenaltyService.php`  
Harmonogram: `routes/console.php` → `Schedule::command('bookings:penalize-unpaid')->hourly()`  
Docker: serwis `scheduler` uruchamia `php artisan schedule:work`

### 10.6. Anulowanie rezerwacji przez właściciela

Plik: `app/Http/Controllers/HotelBookingController.php`

```php
public function cancel(Hotel $hotel, Booking $booking): RedirectResponse
{
    $this->hotelAccess->authorizeHotelCapability(auth()->user(), $hotel, HotelWorkerAccess::Bookings);
    $this->bookingService->cancelBooking($booking);
}
```

### 10.7. Zdjęcia — UUID 

Plik: `app/Services/PhotoUploadService.php`

```php
$filename = (string) Str::uuid();
Storage::disk($this->disk())->put($path, $file->get());

return $imageable->photos()->create([
    'filename' => $filename,
    'file_type' => $fileType,
    'order' => $order,
]);
```

Model `Photo` używa relacji `morphTo('imageable')` — zdjęcie może należeć do `Hotel` lub `Room`.

### 10.8. Czat — polling JavaScript

Plik: `public/js/hotel-chat-polling.js`

```javascript
const pollInterval = parseInt(chatRoot.dataset.pollInterval || '60000', 10);
```

### 10.9. REST API

Plik: `routes/api.php`

```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"client@demo.pl","password":"password"}'

curl http://localhost/api/notifications \
  -H "Authorization: Bearer {TOKEN}"
```

Testy API: `tests/Feature/NotificationApiTest.php`

### 10.10. Panel administratora

Trasy w `routes/maciej.php` pod `middleware('permission:0')`:

| Endpoint | Funkcja |
|----------|---------|
| `/manage/admin` | Dashboard |
| `/manage/admin/users` | CRUD użytkowników |
| `/manage/admin/hotels` | CRUD hoteli |
| `/manage/admin/reviews` | Moderacja opinii (ban/unban) |
| `/manage/admin/reports` | Obsługa zgłoszeń |

### 10.11. Automatyczne seedowanie

Plik: `compose.yaml` (serwis `init`):

```bash
php artisan migrate --seed --force --no-interaction
```

Plik: `database/seeders/DemoSeeder.php` — 15 hoteli, pokoje, rezerwacje, opinie, konta demo.

---

## 11. Mapa tras

| URL | Opis |
|-----|------|
| `GET /hotels` | Katalog publiczny |
| `GET /hotels/{hotel}` | Strona hotelu |
| `GET /bookings/...` | Rezerwacje klienta |
| `GET /profile` | Profil użytkownika |
| `GET /manage/hotels` | Panel właściciela |
| `GET /manage/admin` | Panel administratora |
| `GET /manage/hotels/{hotel}/chat` | Czat |
| `GET /manage/hotels/{hotel}/photos` | Zdjęcia hotelu |
| `GET /manage/hotels/{hotel}/rooms/{room}/photos` | Zdjęcia pokoju |
| `POST /api/login` | API — logowanie |

---

## 12. Konta testowe

| Email | Hasło | Opis |
|-------|-------|------|
| `test@example.com` | `password` | Administrator systemu |
| `owner@demo.pl` | `password` | Właściciel hoteli |
| `worker@demo.pl` | `password` | Pracownik hotelu |
| `client@demo.pl` | `password` | Klient z rezerwacjami |
| `client2@demo.pl` | `password` | Klient z opiniami |
| `client3@demo.pl` | `password` | Klient testowy |

---

## 13. Struktura katalogów

```
AI_projekt_System_Rezerwacji_miejsc_hotelowych/
├── app/
│   ├── Console/Commands/       # PenalizeUnpaidBookings
│   ├── Enums/                  # UserPermission, BookingStatus, ...
│   ├── Http/
│   │   ├── Controllers/        # Kontrolery web + Api/
│   │   ├── Middleware/         # Ban, permission, panel hoteli
│   │   ├── Requests/           # Walidacja formularzy
│   │   └── Resources/          # API resources
│   ├── Models/                 # Eloquent
│   ├── Notifications/          # Mail, push, database
│   ├── Policies/               # Autoryzacja zasobów
│   ├── Providers/              # AppServiceProvider (trasy maciej.php)
│   └── Services/               # Logika biznesowa
├── bootstrap/app.php
├── compose.yaml                # Docker: init, laravel.test, scheduler, pgsql, pgadmin
├── config/
├── database/
│   ├── migrations/             # Schemat PostgreSQL (23 migracje)
│   └── seeders/                # DemoSeeder, AmenitySeeder, ...
├── docker/                     # Obrazy Sail (PHP 8.5)
├── public/
│   ├── css/hotel-map.css
│   ├── js/                     # hotel-map-form.js, hotel-map-show.js, hotel-chat-polling.js
│   └── build/                  # Vite assets
├── resources/
│   ├── sass/
│   └── views/                  # Blade: hotels, bookings, admin, layouts, ...
├── routes/
│   ├── web.php                 # Główne trasy
│   ├── maciej.php              # Zdjęcia, opinie, czat, admin
│   ├── api.php                 # REST API (Sanctum)
│   └── console.php             # Scheduler
└── tests/
    └── Feature/                # NotificationApiTest, ChatRecipientTest
```

---

## 14. Testy automatyczne

| Plik | Zakres |
|------|--------|
| `tests/Feature/NotificationApiTest.php` | API powiadomień (Sanctum) |
| `tests/Feature/ChatRecipientTest.php` | Odbiorcy czatu |

Uruchomienie:

```bash
docker compose exec laravel.test php artisan test
```

---

## 15. Kierunki rozwoju

- Prawdziwa bramka płatności (Stripe, Przelewy24) zamiast symulacji.
- Rozszerzenie REST API — hotele, pokoje, rezerwacje (obecnie API obejmuje auth + powiadomienia).
- Kalendarz dostępności w panelu właściciela.
- Powiadomienia dla właściciela o nowych rezerwacjach.
- Eksport rezerwacji do CSV/PDF.
- Wyszukiwanie geograficzne (promień od współrzędnych na mapie).
- Weryfikacja e-mail przy rejestracji (`MustVerifyEmail`).

---

## 16. Dodawanie zdjęć — instrukcja krok po kroku

### Wymagania wstępne

1. Uruchomiony Docker: `docker compose up -d`
2. Zalogowany użytkownik z uprawnieniem **photos** (właściciel, administrator lub pracownik z zaznaczonym uprawnieniem „zdjęcia”)
3. Istniejący hotel (i opcjonalnie pokój)

### Zdjęcia hotelu (przez panel)

1. Zaloguj się np. jako `owner@demo.pl` / `password`.
2. Wejdź w **Panel hoteli**: http://localhost/manage/hotels
3. Przy wybranym hotelu kliknij przycisk **Zdjęcia**.
4. Na stronie `/manage/hotels/{id}/photos`:
   - wybierz plik **JPG** lub **PNG** (max **5 MB**),
   - opcjonalnie ustaw **kolejność** (1 = pierwsze na liście),
   - kliknij **Prześlij zdjęcie**.
5. Zdjęcia pojawią się w galerii na tej stronie oraz na publicznej stronie hotelu (`/hotels/{id}`).

### Zdjęcia pokoju

1. Z panelu hotelu wejdź w **Pokoje** → `/manage/hotels/{id}/rooms`.
2. Przy pokoju kliknij **Zdjęcia**.
3. Na stronie `/manage/hotels/{id}/rooms/{room_id}/photos` prześlij plik tak samo jak dla hotelu.

### Kto może dodawać zdjęcia?

| Konto | Zdjęcia hotelu/pokoju |
|-------|------------------------|
| `owner@demo.pl` | Tak (właściciel) |
| `test@example.com` | Tak (administrator) |
| `worker@demo.pl` | **Nie domyślnie** — brak uprawnienia `photos` w seedzie; właściciel może nadać je w **Pracownicy** |

### Gdzie trafiają pliki?

- Dysk: `public` (`config/photos.php`)
- Katalog: `storage/app/public/photos/`
- Nazwa pliku: **UUID** (np. `a1b2c3d4-....jpg`), kolumna w bazie: `filename` + `file_type`
- Relacja polimorficzna: rekord w tabeli `photos` powiązany z `Hotel` lub `Room`



### Ograniczenia techniczne

| Parametr | Wartość |
|----------|---------|
| Dozwolone formaty | JPG, JPEG, PNG |
| Maks. rozmiar | 5120 KB (5 MB) |
| Konfiguracja | `config/photos.php` |

---
