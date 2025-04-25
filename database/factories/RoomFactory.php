<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create room numbers in format 100-999
        $roomNumber = (string) fake()->numberBetween(100, 999);

        // Room types
        $roomTypes = ['standard', 'deluxe', 'suite', 'executive', 'presidential'];

        return [
            'number' => $roomNumber,
            'type' => fake()->randomElement($roomTypes),
            'is_available' => fake()->boolean(80), // 80% chance of being available
        ];
    }

    /**
     * Indicate that the room is available.
     *
     * @return static
     */
    public function available()
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => true,
        ]);
    }

    /**
     * Indicate that the room is unavailable.
     *
     * @return static
     */
    public function unavailable()
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
