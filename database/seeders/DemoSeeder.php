<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\HotelWorkerAccess;
use App\Enums\PaymentStatus;
use App\Enums\ReportStatus;
use App\Enums\ReportTitle;
use App\Enums\UserPermission;
use App\Models\Booking;
use App\Models\ExtraAmenity;
use App\Models\Hotel;
use App\Models\HotelAmenity;
use App\Models\Message;
use App\Models\Report;
use App\Models\Review;
use App\Models\Room;
use App\Models\RoomAmenity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 *
 *
 * Konta testowe (hasło: password):
 * - test@example.com      — administrator (moderacja opinii / zgłoszeń)
 * - owner@demo.pl         — właściciel hoteli
 * - worker@demo.pl        — pracownik (pierwszy hotel)
 * - client@demo.pl        — klient z rezerwacjami
 * - client2@demo.pl       — klient z opiniami
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AmenitySeeder::class);

        $this->user('test@example.com', 'Admin', 'Systemowy', UserPermission::Administrator);
        $owner = $this->user('owner@demo.pl', 'Anna', 'Kowalska', UserPermission::Owner);
        $worker = $this->user('worker@demo.pl', 'Piotr', 'Nowak', UserPermission::Worker);
        $client = $this->user('client@demo.pl', 'Jan', 'Klient', UserPermission::Client);
        $client2 = $this->user('client2@demo.pl', 'Maria', 'Wiśniewska', UserPermission::Client);
        $client3 = $this->user('client3@demo.pl', 'Tomasz', 'Zieliński', UserPermission::Client);

        $hotels = $this->seedHotels($owner);
        $featuredHotel = $hotels->first();

        $this->attachWorkers($hotels, $owner, $worker);
        $this->seedRooms($hotels, $featuredHotel);
        $this->call(PhotoSeeder::class);
        $this->seedBookingsAndReviews($featuredHotel, $client, $client2, $client3);
        $this->seedMessages($featuredHotel, $client, $owner);
        $this->seedReports($featuredHotel, $client, $client2);

        $this->command?->info('DemoSeeder: 15 hoteli, konta testowe (hasło: password) — owner@demo.pl, client@demo.pl');
    }

    private function user(string $email, string $name, string $lastName, UserPermission $permission): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'last_name' => $lastName,
                'phone' => '500100200',
                'password' => 'password',
                'permission' => $permission,
            ]
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, Hotel>
     */
    private function seedHotels(User $owner)
    {
        $definitions = [
            ['name' => 'Grand Hotel Kraków', 'city' => 'Kraków', 'address' => 'ul. Floriańska 14', 'latitude' => 50.0647, 'longitude' => 19.9450],
            ['name' => 'Baltic View Gdańsk', 'city' => 'Gdańsk', 'address' => 'ul. Długi Targ 28', 'latitude' => 54.3520, 'longitude' => 18.6466],
            ['name' => 'Warsaw Central Suites', 'city' => 'Warszawa', 'address' => 'ul. Marszałkowska 100', 'latitude' => 52.2297, 'longitude' => 21.0122],
            ['name' => 'Tatra Resort Zakopane', 'city' => 'Zakopane', 'address' => 'ul. Krupówki 12', 'latitude' => 49.2992, 'longitude' => 19.9496],
            ['name' => 'Odra Park Wrocław', 'city' => 'Wrocław', 'address' => 'ul. Świdnicka 8', 'latitude' => 51.1079, 'longitude' => 17.0385],
            ['name' => 'Poznań Old Town Inn', 'city' => 'Poznań', 'address' => 'ul. Św. Marcin 45', 'latitude' => 52.4064, 'longitude' => 16.9252],
            ['name' => 'Łódź Fabryczna Hotel', 'city' => 'Łódź', 'address' => 'ul. Piotrkowska 86', 'latitude' => 51.7592, 'longitude' => 19.4560],
            ['name' => 'Sopot Marina Spa', 'city' => 'Sopot', 'address' => 'ul. Bohaterów Monte Cassino 5', 'latitude' => 54.4416, 'longitude' => 18.5601],
            ['name' => 'Lublin Crown', 'city' => 'Lublin', 'address' => 'ul. Krakowskie Przedmieście 4', 'latitude' => 51.2465, 'longitude' => 22.5684],
            ['name' => 'Katowice Business Hub', 'city' => 'Katowice', 'address' => 'ul. Mariacka 15', 'latitude' => 50.2649, 'longitude' => 19.0238],
            ['name' => 'Rzeszów Airport Lodge', 'city' => 'Rzeszów', 'address' => 'ul. Jana Pawła II 50', 'latitude' => 50.0412, 'longitude' => 21.9991],
            ['name' => 'Białystok Green Park', 'city' => 'Białystok', 'address' => 'ul. Lipowa 10', 'latitude' => 53.1325, 'longitude' => 23.1688],
            ['name' => 'Szczecin Harbor Hotel', 'city' => 'Szczecin', 'address' => 'ul. Wały Chrobrego 1', 'latitude' => 53.4285, 'longitude' => 14.5528],
            ['name' => 'Toruń Gothic Stay', 'city' => 'Toruń', 'address' => 'ul. Szeroka 18', 'latitude' => 53.0138, 'longitude' => 18.5984],
            ['name' => 'Kielce Świętokrzyski Resort', 'city' => 'Kielce', 'address' => 'ul. Henryka Sienkiewicza 22', 'latitude' => 50.8661, 'longitude' => 20.6286],
        ];

        $hotels = collect();

        foreach ($definitions as $index => $definition) {
            $hotel = Hotel::query()->create([
                'owner_id' => $index < 5 ? $owner->id : null,
                'name' => $definition['name'],
                'description' => 'Obiekt demonstracyjny z pełną ofertą pokoi, udogodnień i obsługi rezerwacji online. '
                    .'Idealny do prezentacji katalogu, mapy i panelu zarządzania.',
                'city' => $definition['city'],
                'address' => $definition['address'],
                'latitude' => $definition['latitude'],
                'longitude' => $definition['longitude'],
            ]);

            $this->attachHotelAmenities($hotel);
            $hotels->push($hotel);
        }

        return $hotels;
    }

    private function attachHotelAmenities(Hotel $hotel): void
    {
        $amenityIds = \App\Models\Amenity::query()->pluck('id');

        if ($amenityIds->isEmpty()) {
            return;
        }

        $selected = $amenityIds->random(min($amenityIds->count(), rand(4, 7)));

        foreach ($selected as $amenityId) {
            $hotel->amenities()->attach($amenityId, [
                'price' => rand(0, 100) < 45 ? 0 : rand(15, 80),
            ]);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Hotel>  $hotels
     */
    private function attachWorkers($hotels, User $owner, User $worker): void
    {
        $fullPermissions = HotelWorkerAccess::values();

        $hotels->take(5)->each(function (Hotel $hotel) use ($owner, $fullPermissions): void {
            $hotel->workers()->syncWithoutDetaching([
                $owner->id => ['permissions' => $fullPermissions],
            ]);
        });

        $firstHotel = $hotels->first();

        if ($firstHotel !== null) {
            $firstHotel->workers()->syncWithoutDetaching([
                $worker->id => [
                    'permissions' => [
                        HotelWorkerAccess::Rooms->value,
                        HotelWorkerAccess::Bookings->value,
                        HotelWorkerAccess::Chat->value,
                    ],
                ],
            ]);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Hotel>  $hotels
     */
    private function seedRooms($hotels, Hotel $featuredHotel): void
    {
        $featuredRooms = [
            ['name' => 'Pokój Standard 2-os.', 'capacity' => 2, 'price_per_night' => 249.00, 'quantity' => 10],
            ['name' => 'Pokój Deluxe', 'capacity' => 2, 'price_per_night' => 349.00, 'quantity' => 8],
            ['name' => 'Apartament Junior', 'capacity' => 3, 'price_per_night' => 449.00, 'quantity' => 5],
            ['name' => 'Apartament Rodzinny', 'capacity' => 4, 'price_per_night' => 549.00, 'quantity' => 4],
            ['name' => 'Studio Economy', 'capacity' => 1, 'price_per_night' => 179.00, 'quantity' => 12],
            ['name' => 'Pokój Premium z widokiem', 'capacity' => 2, 'price_per_night' => 399.00, 'quantity' => 6],
            ['name' => 'Suite Business', 'capacity' => 2, 'price_per_night' => 599.00, 'quantity' => 3],
            ['name' => 'Pokój Single', 'capacity' => 1, 'price_per_night' => 199.00, 'quantity' => 15],
        ];

        foreach ($featuredRooms as $roomData) {
            $room = $featuredHotel->rooms()->create([
                ...$roomData,
                'description' => 'Przestronny pokój z łazienką, telewizorem i dostępem do udogodnień obiektu.',
            ]);

            $this->attachRoomAmenities($room);
        }

        $hotels->skip(1)->each(function (Hotel $hotel): void {
            Room::factory(rand(2, 4))->create([
                'hotel_id' => $hotel->id,
            ])->each(fn (Room $room) => $this->attachRoomAmenities($room));
        });
    }

    private function attachRoomAmenities(Room $room): void
    {
        $hotelAmenities = HotelAmenity::query()
            ->where('hotel_id', $room->hotel_id)
            ->get();

        if ($hotelAmenities->isEmpty()) {
            return;
        }

        $selected = $hotelAmenities->random(min($hotelAmenities->count(), rand(2, 5)));

        foreach ($selected as $hotelAmenity) {
            $basePrice = (float) $hotelAmenity->price;
            $price = $basePrice > 0 && rand(0, 100) < 60
                ? $basePrice
                : 0.0;

            RoomAmenity::query()->create([
                'room_id' => $room->id,
                'hotel_amenity_id' => $hotelAmenity->id,
                'price' => $price,
            ]);
        }
    }

    private function seedBookingsAndReviews(Hotel $hotel, User $client, User $client2, User $client3): void
    {
        $rooms = $hotel->rooms()->orderBy('id')->get();

        if ($rooms->count() < 3) {
            return;
        }

        $roomA = $rooms[0];
        $roomB = $rooms[1];
        $roomC = $rooms[2];

        $upcoming = Booking::query()->create([
            'user_id' => $client->id,
            'room_id' => $roomA->id,
            'check_in' => Carbon::today()->addDays(7),
            'check_out' => Carbon::today()->addDays(10),
            'total_price' => 747.00,
            'payment_status' => PaymentStatus::Pending,
            'status' => BookingStatus::Active,
        ]);

        $paidAmenity = RoomAmenity::query()
            ->where('room_id', $roomA->id)
            ->where('price', '>', 0)
            ->first();

        if ($paidAmenity !== null) {
            ExtraAmenity::query()->create([
                'booking_id' => $upcoming->id,
                'hotel_amenity_id' => $paidAmenity->hotel_amenity_id,
                'price' => $paidAmenity->price,
            ]);
        }

        $completed = Booking::query()->create([
            'user_id' => $client->id,
            'room_id' => $roomB->id,
            'check_in' => Carbon::today()->subDays(14),
            'check_out' => Carbon::today()->subDays(10),
            'total_price' => 1396.00,
            'payment_status' => PaymentStatus::Paid,
            'status' => BookingStatus::Completed,
        ]);

        Review::query()->create([
            'booking_id' => $completed->id,
            'user_id' => $client->id,
            'hotel_id' => $hotel->id,
            'rating' => 5,
            'comment' => 'Świetna lokalizacja, czyste pokoje i szybka obsługa. Polecam!',
            'is_banned' => false,
        ]);

        Booking::query()->create([
            'user_id' => $client2->id,
            'room_id' => $roomC->id,
            'check_in' => Carbon::today()->subDays(30),
            'check_out' => Carbon::today()->subDays(27),
            'total_price' => 1347.00,
            'payment_status' => PaymentStatus::Paid,
            'status' => BookingStatus::Completed,
        ]);

        Review::query()->create([
            'booking_id' => null,
            'user_id' => $client2->id,
            'hotel_id' => $hotel->id,
            'rating' => 4,
            'comment' => 'Bardzo dobry hotel na city break. Śniadania mogłyby być bardziej różnorodne.',
            'is_banned' => false,
        ]);

        Review::query()->create([
            'booking_id' => null,
            'user_id' => $client3->id,
            'hotel_id' => $hotel->id,
            'rating' => 1,
            'comment' => 'Opinia zmoderowana — niewidoczna publicznie.',
            'is_banned' => true,
        ]);

        Booking::query()->create([
            'user_id' => $client3->id,
            'room_id' => $roomA->id,
            'check_in' => Carbon::today()->addDays(20),
            'check_out' => Carbon::today()->addDays(22),
            'total_price' => 498.00,
            'payment_status' => PaymentStatus::Pending,
            'status' => BookingStatus::Cancelled,
        ]);
    }

    private function seedMessages(Hotel $hotel, User $client, User $owner): void
    {
        Message::query()->create([
            'sender_id' => $client->id,
            'receiver_id' => $owner->id,
            'hotel_id' => $hotel->id,
            'content' => 'Dzień dobry, czy można zamówić późne zameldowanie około 22:00?',
            'is_read' => true,
        ]);

        Message::query()->create([
            'sender_id' => $owner->id,
            'receiver_id' => $client->id,
            'hotel_id' => $hotel->id,
            'content' => 'Oczywiście — prosimy o informację na recepcji w dniu przyjazdu.',
            'is_read' => false,
        ]);
    }

    private function seedReports(Hotel $hotel, User $client, User $client2): void
    {
        Report::query()->create([
            'user_id' => $client->id,
            'hotel_id' => $hotel->id,
            'title' => ReportTitle::HotelNieOdpowiada,
            'reason' => 'Wysłałem pytanie przez czat 3 dni temu i brak odpowiedzi.',
            'status' => ReportStatus::Pending,
        ]);

        Report::query()->create([
            'user_id' => $client2->id,
            'hotel_id' => $hotel->id,
            'title' => ReportTitle::Inne,
            'reason' => 'Problem rozwiązany przez recepcję — zamknięte.',
            'status' => ReportStatus::Resolved,
        ]);
    }
}
