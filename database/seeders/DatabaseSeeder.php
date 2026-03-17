<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Type;
use App\Models\Niveau;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Les type
        Type::factory()->create([
            'name' => 'Apprenti',
            'description' => 'Apprenti',
        ]);
        Type::factory()->create([
            'name' => 'Parent',
            'description' => 'Parent',
        ]);
        Type::factory()->create([
            'name' => 'Admin',
            'description' => 'Admin',
        ]);

        // Les niveaux
        Niveau::factory()->create([
            'numero' => 1,
            'nom' => 'Niveau 1',
            'description' => 'Pour les 3 à 7 ans',
        ]);
        Niveau::factory()->create([
            'numero' => 2,
            'nom' => 'Niveau 2',
            'description' => 'Pour les 8 à 12 ans',
        ]);
        Niveau::factory()->create([
            'numero' => 3,
            'nom' => 'Niveau 3',
            'description' => 'Pour les 13 à 17 ans',
        ]);

        User::factory()->create([
            'code' => 'ADMIN01USER',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'type_id' => 3,
        ]);
        User::factory()->create([
            'code' => 'TEST12USER',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'type_id' => 1,
        ]);

        // $this->call(AllDataSeeder::class); //envoie des données
    }
}
