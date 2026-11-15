<?php

namespace Tests\Feature\Database;

use App\Models\CognitiveMetric;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_seeder_creates_admin_and_seven_days_of_cognitive_metrics(): void
    {
        $this->travelTo(Carbon::parse('2026-10-25 10:00:00', 'Asia/Jakarta'));

        $this->seed();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name' => 'Administrator Radar Kognitif',
            'email' => 'admin@radarkognitif.mil.id',
        ]);
        $this->assertDatabaseCount('cognitive_metrics', 7);
        $this->assertTrue(CognitiveMetric::whereDate('tanggal', '2026-10-19')->exists());
        $this->assertSame(
            23,
            CognitiveMetric::whereDate('tanggal', '2026-10-25')->value('jumlah_bot_terdeteksi'),
        );
        $this->assertDatabaseHas('berita_osint', [
            'judul' => '[SIMULASI X/TWITTER] Hoaks keamanan siber beredar di Jayapura // DEMO-001',
            'status_alert' => 'Merah',
        ]);

        $admin = User::where('email', 'admin@radarkognitif.mil.id')->firstOrFail();

        $this->assertTrue(Hash::check('password123', $admin->password));
    }

    public function test_production_seeder_does_not_create_the_shared_admin_account(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        app(DatabaseSeeder::class)->run();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseHas('berita_osint', [
            'judul' => '[SIMULASI X/TWITTER] Hoaks keamanan siber beredar di Jayapura // DEMO-001',
        ]);
    }
}
