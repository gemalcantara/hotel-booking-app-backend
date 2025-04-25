<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 random rooms
        Room::factory(10)->create();

        // Create 5 guaranteed available rooms
        Room::factory(5)->available()->create();

        // Create 2 unavailable rooms
        Room::factory(2)->unavailable()->create();

        // Create specific room types
        $roomTypes = ['standard', 'deluxe', 'suite', 'executive', 'presidential'];
        
        foreach ($roomTypes as $type) {
            Room::factory()->create([
                'type' => $type,
                'is_available' => true,
            ]);
        }

        $this->command->info('Generated ' . Room::count() . ' rooms for testing');
    }
}
