




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


Osoba 1: Moduł "Hotel, Pokoje i Mapa" (Core) Odpowiadasz za to, aby hotele w ogóle istniały w systemie i dało się je znaleźć.

Baza danych (Migracje/Modele): Tabele hotels, rooms, amenities (udogodnienia), oraz tabela łącząca hotel_amenity lub room_amenity.

Kontrolery: Pełny CRUD dla hoteli i pokoi. To tutaj znajdzie się logika przypisywania udogodnień i filtrowania na liście.

Widoki: Formularz dodawania hotelu, widok szczegółów hotelu i pokoi.

Mapa: Integracja np. z darmowym Leaflet.js. Właściciel przy dodawaniu hotelu klika na mapę (zapisujesz współrzędne w DB), a na profilu hotelu wyświetla się pinezka.

Udogodnienia: Osobna tabelka słownikowa (np. WiFi, Basen, Parking) i relacja Many-to-Many (tabela pivot).

Osoba 2: Moduł "Multimediów, Komunikacji i Ocen" (Interakcje) Odpowiadasz za to, aby aplikacja żyła – zdjęcia, komentarze, zgłoszenia i czat.

Baza danych (Migracje/Modele): Tabele photos, relacja polimorficzna lub pivot hotel_photos/room_photos, comments, reports (zgłoszenia), messages (czat).

Kontrolery: Obsługa wgrywania plików, dodawanie ocen, CRUD zgłoszeń i wiadomości. Logika banowania komentarzy (np. flaga is_banned w bazie).

Widoki: Galeria zdjęć w hotelu, sekcja komentarzy, okno czatu, panel admina do zgłoszeń.

Zdjęcia: Zapisywane w katalogu public/ (użyj Storage::disk('public')). Generowanie nazwy pliku przez Str::uuid() + rozszerzenie (np. 123e4567-e89b-12d3-a456-426614174000.jpg). W tabeli pivot przechowuj pole order (kolejność zdjęć).

Czat: Bez WebSockets. Zwykły JavaScript na froncie z funkcją setInterval(), która co 60 sekund uderza do API/kontrolera po nowe wiadomości i odświeża widok. Możliwość pisania do samego siebie to po prostu brak blokady w walidacji "czy nadawca = odbiorca".

Osoba 3: Moduł "Rezerwacji, Użytkowników i Uprawnień" (Transakcje) Odpowiadasz za użytkowników, ich role i to, po co ta aplikacja w ogóle istnieje – rezerwacje.

Baza danych (Migracje/Modele): Tabele users, roles/permissions (tabela łącząca 0-5 uprawnień), bookings (rezerwacje).

Kontrolery: Logika logowania/rejestracji (może być rozszerzony Laravel Breeze), CRUD rezerwacji, logika nadawania ról.

Widoki: Panel użytkownika, formularz rezerwacji, panel admina do zarządzania uprawnieniami.

Uprawnienia: Tabela permissions (np. 1-odczyt, 2-edycja, 5-admin) i tabela pivot user_permission.


