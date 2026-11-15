<?php

namespace Database\Seeders;

use App\Models\BeritaOsint;
use App\Models\CognitiveMetric;
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

        if (! app()->isProduction()) {
            User::firstOrCreate(
                ['email' => 'admin@radarkognitif.mil.id'],
                [
                    'name' => 'Administrator Radar Kognitif',
                    'password' => 'password123',
                ],
            );
        }

        $demoReports = [
            [
                'judul' => '[SIMULASI X/TWITTER] Hoaks keamanan siber beredar di Jayapura // DEMO-001',
                'sumber' => 'X/Twitter Intelligence',
                'status_alert' => 'Merah',
                'latitude' => -2.5337,
                'longitude' => 140.7181,
                'minutes_ago' => 18,
            ],
            [
                'judul' => '[SIMULASI THREADS] Narasi disinformasi terpantau di Kupang // DEMO-002',
                'sumber' => 'Threads Narrative Mesh',
                'status_alert' => 'Kuning',
                'latitude' => -10.1772,
                'longitude' => 123.6070,
                'minutes_ago' => 34,
            ],
            [
                'judul' => '[SIMULASI FACEBOOK] Tautan phishing layanan publik di Mataram // DEMO-003',
                'sumber' => 'Facebook Influence Monitor',
                'status_alert' => 'Merah',
                'latitude' => -8.5833,
                'longitude' => 116.1167,
                'minutes_ago' => 51,
            ],
            [
                'judul' => '[SIMULASI TIKTOK] Percakapan keamanan digital meningkat di Bandung // DEMO-004',
                'sumber' => 'TikTok Viral Graph',
                'status_alert' => 'Kuning',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'minutes_ago' => 67,
            ],
            [
                'judul' => '[SIMULASI INSTAGRAM] Aktivitas informasi normal di Surabaya // DEMO-005',
                'sumber' => 'IG Dark Web',
                'status_alert' => 'Aman',
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'minutes_ago' => 83,
            ],
        ];

        foreach ($demoReports as $report) {
            BeritaOsint::updateOrCreate(
                ['judul' => $report['judul']],
                [
                    'sumber' => $report['sumber'],
                    'link' => null,
                    'waktu_publish' => now('Asia/Jakarta')->subMinutes($report['minutes_ago']),
                    'status_alert' => $report['status_alert'],
                    'latitude' => $report['latitude'],
                    'longitude' => $report['longitude'],
                ],
            );
        }

        $metricHarian = [
            [
                'sentimen_positif' => 6200,
                'sentimen_negatif' => 3100,
                'sentimen_netral' => 8700,
                'jumlah_bot_terdeteksi' => 11,
                'trending_issues' => [
                    ['tag' => '#KeamananDigital', 'jumlah_mention' => 4210, 'pertumbuhan' => 42, 'tingkat' => 'Rendah', 'periode' => '60 menit'],
                    ['tag' => '#HoaksNasional', 'jumlah_mention' => 2980, 'pertumbuhan' => 61, 'tingkat' => 'Sedang', 'periode' => '60 menit'],
                ],
            ],
            [
                'sentimen_positif' => 5900,
                'sentimen_negatif' => 3600,
                'sentimen_netral' => 8500,
                'jumlah_bot_terdeteksi' => 14,
                'trending_issues' => [
                    ['tag' => '#Disinformasi', 'jumlah_mention' => 5340, 'pertumbuhan' => 78, 'tingkat' => 'Sedang', 'periode' => '45 menit'],
                    ['tag' => '#DataPribadi', 'jumlah_mention' => 3710, 'pertumbuhan' => 55, 'tingkat' => 'Rendah', 'periode' => '60 menit'],
                ],
            ],
            [
                'sentimen_positif' => 5700,
                'sentimen_negatif' => 4200,
                'sentimen_netral' => 8600,
                'jumlah_bot_terdeteksi' => 16,
                'trending_issues' => [
                    ['tag' => '#PropagandaDigital', 'jumlah_mention' => 6890, 'pertumbuhan' => 104, 'tingkat' => 'Tinggi', 'periode' => '30 menit'],
                    ['tag' => '#KeamananSiber', 'jumlah_mention' => 4920, 'pertumbuhan' => 67, 'tingkat' => 'Sedang', 'periode' => '45 menit'],
                ],
            ],
            [
                'sentimen_positif' => 6100,
                'sentimen_negatif' => 3900,
                'sentimen_netral' => 9000,
                'jumlah_bot_terdeteksi' => 13,
                'trending_issues' => [
                    ['tag' => '#NarasiPalsu', 'jumlah_mention' => 7210, 'pertumbuhan' => 118, 'tingkat' => 'Tinggi', 'periode' => '30 menit'],
                    ['tag' => '#LiterasiDigital', 'jumlah_mention' => 4430, 'pertumbuhan' => 49, 'tingkat' => 'Rendah', 'periode' => '60 menit'],
                ],
            ],
            [
                'sentimen_positif' => 6400,
                'sentimen_negatif' => 4400,
                'sentimen_netral' => 9200,
                'jumlah_bot_terdeteksi' => 18,
                'trending_issues' => [
                    ['tag' => '#ManipulasiAI', 'jumlah_mention' => 8460, 'pertumbuhan' => 132, 'tingkat' => 'Tinggi', 'periode' => '30 menit'],
                    ['tag' => '#Deepfake', 'jumlah_mention' => 5680, 'pertumbuhan' => 96, 'tingkat' => 'Sedang', 'periode' => '45 menit'],
                ],
            ],
            [
                'sentimen_positif' => 6500,
                'sentimen_negatif' => 4900,
                'sentimen_netral' => 9300,
                'jumlah_bot_terdeteksi' => 21,
                'trending_issues' => [
                    ['tag' => '#SeranganSiber', 'jumlah_mention' => 10240, 'pertumbuhan' => 174, 'tingkat' => 'Kritis', 'periode' => '20 menit'],
                    ['tag' => '#BotNetwork', 'jumlah_mention' => 6170, 'pertumbuhan' => 121, 'tingkat' => 'Tinggi', 'periode' => '30 menit'],
                ],
            ],
            [
                'sentimen_positif' => 6263,
                'sentimen_negatif' => 4605,
                'sentimen_netral' => 7552,
                'jumlah_bot_terdeteksi' => 23,
                'trending_issues' => [
                    ['tag' => '#KebocoranData', 'jumlah_mention' => 12840, 'pertumbuhan' => 216, 'tingkat' => 'Kritis', 'periode' => '15 menit'],
                    ['tag' => '#SeranganSiber', 'jumlah_mention' => 8291, 'pertumbuhan' => 148, 'tingkat' => 'Tinggi', 'periode' => '30 menit'],
                    ['tag' => '#ManipulasiAI', 'jumlah_mention' => 4670, 'pertumbuhan' => 87, 'tingkat' => 'Sedang', 'periode' => '60 menit'],
                ],
            ],
        ];

        $tanggalHariIni = now('Asia/Jakarta')->startOfDay();

        foreach ($metricHarian as $index => $metric) {
            CognitiveMetric::updateOrCreate(
                ['tanggal' => $tanggalHariIni->copy()->subDays(6 - $index)],
                $metric,
            );
        }
    }
}
