<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Delete any existing tokens for this user
        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
        
        // Insert hardcoded token directly into the personal_access_tokens table
        // The value '12345' is hashed as that's how Sanctum stores tokens
        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'test-token',
            'token' => hash('sha256', '12345'),
            'abilities' => '["*"]',
            'created_at' => now(),
            'updated_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // Output instructions
        $this->command->info('Test user created with hardcoded API token.');
        $this->command->info('For testing, use: Bearer 12345');
    }
}
