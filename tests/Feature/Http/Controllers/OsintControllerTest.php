<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\BeritaOsint;
use App\Models\CognitiveMetric;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OsintControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_operator_can_view_cognitive_dashboard_with_profile_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Operator Siber',
            'email' => 'operator@example.test',
        ]);
        BeritaOsint::create([
            'judul' => 'Intrusi kritis terdeteksi di sektor komando',
            'sumber' => 'MILITER EYES Sensor Grid',
            'link' => 'https://example.test/intel/kritis',
            'waktu_publish' => now(),
            'status_alert' => 'Merah',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertViewIs('dashboard.index')
            ->assertSee('MILITER EYES')
            ->assertSee('Operator Siber')
            ->assertSee('operator@example.test')
            ->assertSee('ANALISIS SIBER SOSIAL')
            ->assertSee('BELUM ADA DATA SENTIMEN DARI SUMBER TERHUBUNG')
            ->assertSee('BELUM ADA DATA JEJARING SOSIAL')
            ->assertSee('FILTER LAYER')
            ->assertSee('data-map-filter="Merah"', false)
            ->assertSee('L.layerGroup()', false)
            ->assertSee('L.divIcon', false)
            ->assertSee('Intrusi kritis terdeteksi di sektor komando')
            ->assertDontSee('AI FUSION ONLINE')
            ->assertDontSee('87.4%')
            ->assertSee('STATUS PEMINDAIAN')
            ->assertSee('EKSPOR INTELIJEN (PDF)')
            ->assertSee(route('osint.export.pdf'))
            ->assertSee('const liveMetricsUrl', false)
            ->assertSee('BACA DOKUMEN')
            ->assertSee('LOGOUT')
            ->assertDontSee('RADAR KOGNITIF');
    }

    public function test_dashboard_renders_telegram_test_form_with_post_route_and_csrf_token(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('action="'.route('osint.test.telegram').'"', false)
            ->assertSee('method="POST"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_dashboard_renders_the_latest_cognitive_metric_from_the_database(): void
    {
        $user = User::factory()->create();
        CognitiveMetric::create([
            'tanggal' => '2026-10-24',
            'sentimen_positif' => 10,
            'sentimen_negatif' => 70,
            'sentimen_netral' => 20,
            'jumlah_bot_terdeteksi' => 4,
            'trending_issues' => [
                ['tag' => '#IsuLama', 'jumlah_mention' => 100, 'pertumbuhan' => 10, 'tingkat' => 'Rendah', 'periode' => '60 menit'],
            ],
        ]);
        CognitiveMetric::create([
            'tanggal' => '2026-10-25',
            'sentimen_positif' => 250,
            'sentimen_negatif' => 250,
            'sentimen_netral' => 500,
            'jumlah_bot_terdeteksi' => 12,
            'trending_issues' => [
                ['tag' => '#AncamanTerkini', 'jumlah_mention' => 9500, 'pertumbuhan' => 120, 'tingkat' => 'Kritis', 'periode' => '15 menit'],
            ],
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertViewHas('metricKognitifTerbaru', fn (CognitiveMetric $metric): bool => $metric->tanggal->toDateString() === '2026-10-25')
            ->assertViewHas('persentaseSentimen', [
                'positif' => 25.0,
                'negatif' => 25.0,
                'netral' => 50.0,
            ])
            ->assertSee('#AncamanTerkini')
            ->assertSee('9.500 mention')
            ->assertSee('12')
            ->assertDontSee('#IsuLama');
    }

    public function test_fetch_imports_rss_reports_without_inventing_coordinates(): void
    {
        $user = User::factory()->create();

        Http::preventStrayRequests();
        Http::fake([
            'https://news.google.com/rss/search?q=cybersecurity%20indonesia&hl=id&gl=ID&ceid=ID:id' => Http::response(
                $this->rss('Insiden keamanan siber Jakarta', 'https://example.com/google-cyber'),
                200,
                ['Content-Type' => 'application/xml'],
            ),
            'https://news.google.com/rss/search?q=serangan%20siber%20tni&hl=id&gl=ID&ceid=ID:id' => Http::response(
                $this->rss('Ancaman digital di Papua', 'https://example.com/google-tni'),
                200,
                ['Content-Type' => 'application/xml'],
            ),
            'https://www.antaranews.com/rss/terkini.xml' => Http::response(
                $this->rss('Perkembangan nasional terbaru', 'https://example.com/antara'),
                200,
                ['Content-Type' => 'text/xml'],
            ),
            'https://www.cnnindonesia.com/nasional/rss' => Http::response(
                $this->rss('Laporan teknologi dari Surabaya', 'https://example.com/cnn'),
                200,
                ['Content-Type' => 'text/xml'],
            ),
            'https://www.cnbcindonesia.com/news/rss' => Http::response(
                $this->rss('Kabar strategis Natuna', 'https://example.com/cnbc'),
                200,
                ['Content-Type' => 'text/xml'],
            ),
            'https://www.tribunnews.com/rss' => Http::response(
                $this->rss('Laporan keamanan dari Papua', 'https://example.com/tribun'),
                200,
                ['Content-Type' => 'text/xml'],
            ),
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('osint.index'))
            ->post(route('osint.fetch'));

        $response
            ->assertRedirect(route('osint.index'))
            ->assertSessionHas(
                'success',
                'Pemindaian selesai. 6 laporan dari feed RSS publik diproses; sumber media sosial belum terhubung.',
            );

        $this->assertDatabaseCount('berita_osint', 6);
        $this->assertDatabaseHas('berita_osint', [
            'judul' => 'Insiden keamanan siber Jakarta',
            'sumber' => 'Google News - Cybersecurity Indonesia',
        ]);
        $this->assertDatabaseHas('berita_osint', [
            'judul' => 'Kabar strategis Natuna',
            'sumber' => 'CNBC Indonesia',
        ]);
        $this->assertDatabaseHas('berita_osint', [
            'judul' => 'Laporan keamanan dari Papua',
            'sumber' => 'Tribun News',
        ]);
        $this->assertSame(0, BeritaOsint::where('judul', 'like', '[SIMULASI%')->count());

        $beritaJakarta = BeritaOsint::where('judul', 'Insiden keamanan siber Jakarta')->firstOrFail();
        $beritaFallback = BeritaOsint::where('judul', 'Perkembangan nasional terbaru')->firstOrFail();

        $this->assertSame(-6.2088, (float) $beritaJakarta->latitude);
        $this->assertSame(106.8456, (float) $beritaJakarta->longitude);
        $this->assertNull($beritaFallback->latitude);
        $this->assertNull($beritaFallback->longitude);

        Http::assertSentCount(6);
    }

    public function test_authenticated_operator_can_poll_live_dashboard_metrics(): void
    {
        $user = User::factory()->create();
        BeritaOsint::create([
            'judul' => 'Ancaman kritis polling',
            'sumber' => 'Test Sensor',
            'waktu_publish' => now(),
            'status_alert' => 'Merah',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        $response = $this->actingAs($user)->getJson(route('osint.live-metrics'));

        $response
            ->assertOk()
            ->assertJsonPath('counts.total', 1)
            ->assertJsonPath('counts.merah', 1)
            ->assertJsonStructure([
                'generated_at',
                'counts' => ['total', 'merah', 'kuning', 'aman'],
                'map_counts' => ['total', 'merah', 'kuning', 'aman'],
                'threat_trend' => ['labels', 'merah', 'kuning', 'aman'],
                'cognitive_trend' => ['labels', 'positif', 'negatif', 'netral'],
                'sentiment' => ['percentages', 'total', 'bots'],
            ])
            ->assertJsonPath('map_counts.total', 1)
            ->assertJsonPath('map_counts.merah', 1);
    }

    public function test_authenticated_operator_can_export_top_secret_pdf(): void
    {
        $user = User::factory()->create(['name' => 'Analis PDF']);
        BeritaOsint::create([
            'judul' => 'Ancaman prioritas untuk dokumen terklasifikasi',
            'sumber' => 'MILITER EYES Sensor',
            'waktu_publish' => now(),
            'status_alert' => 'Merah',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);

        $response = $this->actingAs($user)->get(route('osint.export.pdf'));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'attachment; filename=MILITER-EYES-TOP-SECRET-',
            (string) $response->headers->get('content-disposition'),
        );
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_live_metrics_and_pdf_export_require_authentication(): void
    {
        $this->getJson(route('osint.live-metrics'))->assertUnauthorized();
        $this->get(route('osint.export.pdf'))->assertRedirect(route('login'));
    }

    private function rss(string $title, string $link): string
    {
        return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <rss version="2.0">
                <channel>
                    <title>Test Feed</title>
                    <item>
                        <title>{$title}</title>
                        <link>{$link}</link>
                        <pubDate>Sat, 24 Oct 2026 08:00:00 +0700</pubDate>
                    </item>
                </channel>
            </rss>
            XML;
    }
}
