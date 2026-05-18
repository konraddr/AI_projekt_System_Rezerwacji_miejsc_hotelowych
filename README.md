




AI_projekt_System_Rezerwacji_miejsc_hotelowych
## ️ Jak uruchomić projekt
## 1. Co musisz mieć włączone
* Docker Desktop (uruchomiony i działający w tle).
* Git (do pobrania kodu).
* Linux/WSL (Jeśli masz Windowsa, używaj terminala Ubuntu lub wpisuj komendy przez wsl).


## 2. Sklonuj repozytorium
* Wpisz w terminalu:
>
> git clone https://github.com/konraddr/AI_projekt_System_Rezerwacji_miejsc_hotelowych.git
>
> cd AI_projekt_System_Rezerwacji_miejsc_hotelowych
>

## 3. Skopiuj plik konfiguracyjny i pobierz paczki
   * Wklej po kolei:
> cp .env.example .env
> 
> docker run --rm -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install
>
## 4. Odpal serwer
   * Włączamy Dockera poleceniem Sail:
   > ./vendor/bin/sail up -d
## 5. Wgraj bazę danych i wygląd
   Gdy serwer działa, wklej po kolei te 4 komendy, aby wszystko zainstalować:

>./vendor/bin/sail artisan key:generate

> ./vendor/bin/sail artisan migrate 

>./vendor/bin/sail npm install

>./vendor/bin/sail npm run build
 
Podział:

###  OSOBA 1: Konrad (Tech Lead - Moduł "Hotel, Pokoje i Mapa")
Odpowiadasz za architekturę rdzenia systemu, główną bazę danych oraz zarządzanie katalogiem noclegowym.

* **Baza danych (Tabele)**: `hotels`, `rooms`, `amenities`, `hotel_amenity`, `room_amenities`.
* **Zadania w Laravelu**: 
  - Pełny CRUD dla hoteli i pokoi. 
  - Osobne widoki: publiczny dla Klienta (tylko odczyt) oraz zamknięty panel zarządzania dla Właściciela.
  - Logika dziedziczenia udogodnień i przypisywania do nich cen dla konkretnych pokoi.
  - Mapa (Leaflet.js): Integracja z interaktywną mapą na frontendzie. Właściciel podczas dodawania hotelu klika na mapę, a kod JS pobiera i zapisuje w ukrytym formularzu współrzędne (`latitude`, `longitude`) do bazy. Na profilu hotelu wyświetla się wygenerowana pinezka na mapie.

### OSOBA 2: Maciej (Moduł "Multimediów, Komunikacji i Ocen")
Odpowiadasz za interaktywną część aplikacji, zbieranie opinii oraz komunikację na linii właściciel-klient.

* **Baza danych (Tabele)**: `photos`, `hotel_photos`, `room_photos`, `reviews`, `reports`, `messages`.
* **Zadania w Laravelu**: 
  - CRUD dla opinii, zgłoszeń (ticketów), wiadomości z czatu.
  - Panel administracyjny do zarządzania zgłoszeniami ("hotel nie odpowiada").
  - Zdjęcia (Polimorfizm i UUIDv4): Zapis fizyczny plików w katalogu `public/` (użycie `Storage::disk('public')`). Nazwa pliku obowiązkowo generowana przez `Str::uuid()`. W bazie kolumna ma się nazywać `filename` (nie *filepath*). Tabela przechowuje typ pliku (`file_type`) oraz kolejność wyświetlania (`order`).
  - Czat (JS Polling bez WebSockets): Implementacja czatu na zwykłym JavaScript. Funkcja `setInterval()` ma co 60 sekund asynchronicznie odpytywać kontroler (AJAX) o nowe wiadomości. Dopuszczalna możliwość pisania wiadomości do samego siebie.
  - Moderacja Komentarzy: Funkcja ukrywania/banowania toksycznych komentarzy przez administratora (flaga `is_banned` w tabeli `reviews`).

### OSOBA 3: Daniel (Moduł "Rezerwacji, Użytkowników i Uprawnień")
Odpowiadasz za serce biznesowe aplikacji – transakcje, mechanizmy bezpieczeństwa, kary oraz role użytkowników.

* **Baza danych (Tabele)**: `users`, `workers`, `bookings`, `extra_amenities`.
* **Zadania w Laravelu**: 
  - Logika koszyka i rezerwacji (w tym algorytmy blokujące nakładanie się dat rezerwacji).
  - Autoryzacja i logowanie (może być rozszerzony Laravel Breeze).
  - Uprawnienia i Role: Realizacja uprawnień za pomocą kolumny `permission` (wartości 0-6, gdzie np. 0 to Administrator, a 6 to całkowity BAN nałożony na użytkownika).
    -Zamrażanie Cen: Gdy klient rezerwuje pokój z płatnymi udogodnieniami dodatkowymi, ich dzisiejsza cena z momentu kliknięcia "Rezerwuj" musi zostać skopiowana do tabeli `extra_amenities`. Dzięki temu ewentualna przyszła zmiana cen w hotelu nie wpłynie na koszty już dokonanej rezerwacji klienta.
  - utomatyczny Ban (Kary za brak wpłaty): Zastosowanie mechanizmu , który cyklicznie skanuje bazę. Jeśli użytkownik ma rezerwację ze statusem "oczekuje na wpłatę" przez zbyt długi czas, system automatycznie anuluje rezerwację i zmienia jego `permission` na 6 (BAN).
  - Powiadomienia Push (Push Notifications): Zaimplementowanie wbudowanego systemu `Illuminate\Notifications`. Klient musi otrzymać powiadomienie wypychane (na ekranie lub mailowo), gdy status jego rezerwacji się zmieni lub gdy nadejdzie nowa wiadomość na czacie.


### ERD (https://dbdiagram.io/d)
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

// =========================================================================
// Osoba 1:Konrad
// =========================================================================

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

// =========================================================================
//Osoba 2: Maciej
// =========================================================================

Table photos {
  id uuid [pk, note: ' Generowanie przez UUIDv4']
  imageable_id int 
  imageable_type varchar 
  filename varchar 
  file_type varchar [note: 'Rozszerzenie np. jpg, png']
  order int [note: ' Kolejność wyświetlania zdjęć']
  created_at timestamp
  updated_at timestamp
}
Table room_photos{
  rooms_id int [ref:> rooms.id]
  photos_id int [ref:> photos.id]
}

Table hotel_photos{
  hotel_id int [ref:> hotels.id]
  photos_id int [ref:> photos.id]
}

Table reviews {
  id int [pk, increment]
  booking_id int [ref: > bookings.id]
  user_id int [ref: > users.id]
  hotel_id int [ref: > hotels.id]
  rating int
  comment text
  is_banned boolean [default: false, note: ' Ukrywanie toksycznych komentarzy']
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

// =========================================================================
// OSOBA 3: Daniel
// =========================================================================

Table users {
  id int [pk, increment]
  name varchar
  LastName varchar
  Phone varchar 
  email varchar [unique]
  password varchar
  permission int [note: ' Wartości 0-6 gdzie 0 to admin, 6 to ban konta']
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

Table workers{
  worker_id int [ref:> users.id]
  hotel_id int [ref:> hotels.id]
}
