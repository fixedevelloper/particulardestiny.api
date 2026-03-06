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
    }
}
