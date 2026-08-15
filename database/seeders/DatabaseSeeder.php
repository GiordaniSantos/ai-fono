<?php

namespace Database\Seeders;

use App\Models\Fonoaudiologo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Fonoaudiologofactory(10)->create();

        Fonoaudiologofactory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
