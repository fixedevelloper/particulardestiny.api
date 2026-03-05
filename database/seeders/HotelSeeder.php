<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\RoomAvailability;
use App\Models\User;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Room Types
        |--------------------------------------------------------------------------
        */
        $types = [
            'Suite',
            'Appartement',
            'Chambre Standard',
            'Deluxe'
        ];

        foreach ($types as $type) {
            RoomType::create([
                'name' => $type,
                'slug' => Str::slug($type),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Categories
        |--------------------------------------------------------------------------
        */
        $categories = [
            'Luxury',
            'Business',
            'Family'
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
                'slug' => Str::slug($cat),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Features
        |--------------------------------------------------------------------------
        */
        $features = [
            ['name' => 'WiFi'],
            ['name' => 'Climatisation'],
            ['name' => 'TV', ],
            ['name' => 'Piscine'],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Rooms
        |--------------------------------------------------------------------------
        */
        $roomTypes = RoomType::all();
        $categories = Category::all();
        $features = Feature::all();

        for ($i = 1; $i <= 5; $i++) {
            $room = Room::create([
                'title' => "Room $i",
                'slug' => "room-$i",
                'description' => "Belle chambre numéro $i",
                'price' => rand(50, 300),
                'capacity' => rand(1, 4),
                'size' => rand(20, 100),
                'category_id' => $categories->random()->id,
                'room_type_id' => $roomTypes->random()->id,
            ]);

            // Attacher features
            $room->features()->attach(
                $features->random(rand(1, 3))->pluck('id')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Users (client)
        |--------------------------------------------------------------------------
        */
        $user = User::factory()->create([
            'email' => 'client@test.com'
        ]);

        /*
        |--------------------------------------------------------------------------
        | 6. Reservations + Payments
        |--------------------------------------------------------------------------
        */
        $rooms = Room::all();

        foreach ($rooms as $room) {
            $checkIn = Carbon::now()->addDays(rand(1, 10));
            $checkOut = (clone $checkIn)->addDays(rand(1, 5));

            $nights = $checkIn->diffInDays($checkOut);

            $reservation = Reservation::create([
                'user_id' => $user->id,
                'room_id' => $room->id,

                'check_in' => $checkIn,
                'check_out' => $checkOut,

                'adults' => 2,
                'children' => 1,
                'total_guests' => 3,

                'price_per_night' => $room->price,
                'nights' => $nights,
                'subtotal' => $room->price * $nights,
                'tax' => 10,
                'discount' => 0,
                'total_price' => ($room->price * $nights) + 10,

                'status' => 'confirmed',
                'payment_status' => 'paid',
                'confirmed_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $reservation->total_price,
                'method' => 'mobile_money',
                'transaction_id' => Str::random(10),
                'status' => 'paid',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Availability (bloquer dates)
            |--------------------------------------------------------------------------
            */
            $period = \Carbon\CarbonPeriod::create($checkIn, $checkOut);

            foreach ($period as $date) {
                RoomAvailability::updateOrCreate(
                    [
                        'room_id' => $room->id,
                        'date' => $date->format('Y-m-d'),
                    ],
                    [
                        'is_available' => false
                    ]
                );
            }
        }
    }
}
