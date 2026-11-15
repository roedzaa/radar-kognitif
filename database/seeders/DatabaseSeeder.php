<?php

namespace Database\Seeders;

use App\Models\User;
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
        $this->call(KamusKerawananSeeder::class);

        User::updateOrCreate(
            ['email' => User::SHARED_TESTER_EMAIL],
            [
                'name' => 'Tester RADAR KOGNITIF',
                'password' => 'RadarKognitif-Tester-2026',
            ],
        );
    }
}
