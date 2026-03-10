<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'code' => 'TEST12USER',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'type_id' => 3,
        ]);
        User::factory()->create([
            'code' => 'ADMIN01USER',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'type_id' => 1,
        ]);

        $this->call(AllDataSeeder::class); //envoie des données
    }
}
