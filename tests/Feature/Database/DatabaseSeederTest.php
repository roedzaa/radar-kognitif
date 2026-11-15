<?php

namespace Tests\Feature\Database;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeder_creates_shared_tester_without_synthetic_dashboard_data(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name' => 'Tester RADAR KOGNITIF',
            'email' => User::SHARED_TESTER_EMAIL,
        ]);
        $this->assertDatabaseCount('cognitive_metrics', 0);
        $this->assertDatabaseCount('berita_osint', 0);

        $tester = User::where('email', User::SHARED_TESTER_EMAIL)->firstOrFail();

        $this->assertTrue(Hash::check('RadarKognitif-Tester-2026', $tester->password));
    }
}
