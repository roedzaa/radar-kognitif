<?php

namespace App\Http\Controllers;

use App\Models\BeritaOsint;
use App\Models\CognitiveMetric;
use App\Services\OsintScraperService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OsintController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index', $this->dashboardData());
    }

    public function liveMetrics(): JsonResponse
    {
        $data = $this->dashboardData();
        $berita = $data['berita'];

        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'counts' => [
                'total' => $berita->count(),
                'merah' => $berita->where('status_alert', 'Merah')->count(),
                'kuning' => $berita->where('status_alert', 'Kuning')->count(),
                'aman' => $berita->where('status_alert', 'Aman')->count(),
            ],
            'threat_trend' => $data['statistikTren'],
            'cognitive_trend' => $data['statistikKognitif'],
            'sentiment' => [
                'percentages' => $data['persentaseSentimen'],
                'total' => $data['totalSentimen'],
                'bots' => $data['metricKognitifTerbaru']?->jumlah_bot_terdeteksi ?? 0,
            ],
        ]);
    }

    public function exportPdf(): Response
    {
        $tanggal = today(config('app.timezone'));
        $ancamanKritis = BeritaOsint::query()
            ->where('status_alert', 'Merah')
            ->whereDate('waktu_publish', $tanggal)
            ->latest('waktu_publish')
            ->get();
        $ringkasanHariIni = BeritaOsint::query()
            ->whereDate('waktu_publish', $tanggal)
            ->selectRaw('status_alert, COUNT(*) as total')
            ->groupBy('status_alert')
            ->pluck('total', 'status_alert');

        return Pdf::loadView('pdf.laporan', [
            'ancamanKritis' => $ancamanKritis,
            'ringkasanHariIni' => $ringkasanHariIni,
            'tanggal' => $tanggal,
            'nomorDokumen' => 'ME-INTEL-'.$tanggal->format('Ymd').'-'.str_pad((string) $ancamanKritis->count(), 3, '0', STR_PAD_LEFT),
            'operator' => request()->user(),
        ])->setPaper('a4', 'portrait')
            ->download('MILITER-EYES-TOP-SECRET-'.$tanggal->format('Y-m-d').'.pdf');
    }

    public function fetch(OsintScraperService $scraper): RedirectResponse
    {
        $result = $scraper->runFullScan();

        return back()->with(
            'success',
            "Pemindaian Omni-Source berhasil. {$result['rss']} laporan RSS diproses dan {$result['social']} sinyal media sosial sintetis dibuat.",
        );
    }

    public function testTelegram(OsintScraperService $scraper): RedirectResponse
    {
        $scraper->sendTestAlert();

        return back()->with('success', 'Sinyal uji coba darurat berhasil dikirim ke Telegram.');
    }

    /** @return array<string, mixed> */
    private function dashboardData(): array
    {
        $berita = BeritaOsint::query()->latest('waktu_publish')->get();
        $labels = [];
        $merah = [];
        $kuning = [];
        $aman = [];

        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            $date = now()->subDays($daysAgo);
            $dateKey = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $merah[$dateKey] = 0;
            $kuning[$dateKey] = 0;
            $aman[$dateKey] = 0;
        }

        $threatHistory = BeritaOsint::query()
            ->where('waktu_publish', '>=', now()->subDays(6)->startOfDay())
            ->get(['waktu_publish', 'status_alert']);

        foreach ($threatHistory as $item) {
            if (! $item->waktu_publish) {
                continue;
            }

            $dateKey = $item->waktu_publish->format('Y-m-d');

            if (! isset($merah[$dateKey])) {
                continue;
            }

            match ($item->status_alert) {
                'Merah' => $merah[$dateKey]++,
                'Kuning' => $kuning[$dateKey]++,
                'Aman' => $aman[$dateKey]++,
                default => null,
            };
        }

        $statistikTren = [
            'labels' => $labels,
            'merah' => array_values($merah),
            'kuning' => array_values($kuning),
            'aman' => array_values($aman),
        ];

        $riwayatKognitif = CognitiveMetric::query()
            ->latest('tanggal')
            ->limit(7)
            ->get([
                'tanggal',
                'sentimen_positif',
                'sentimen_negatif',
                'sentimen_netral',
                'jumlah_bot_terdeteksi',
                'trending_issues',
            ])
            ->sortBy('tanggal')
            ->values();
        $metricKognitifTerbaru = $riwayatKognitif->last();
        $sentimenPositif = $metricKognitifTerbaru?->sentimen_positif ?? 0;
        $sentimenNegatif = $metricKognitifTerbaru?->sentimen_negatif ?? 0;
        $sentimenNetral = $metricKognitifTerbaru?->sentimen_netral ?? 0;
        $totalSentimen = $sentimenPositif + $sentimenNegatif + $sentimenNetral;
        $persentaseSentimen = [
            'positif' => $totalSentimen > 0 ? round(($sentimenPositif / $totalSentimen) * 100, 1) : 0,
            'negatif' => $totalSentimen > 0 ? round(($sentimenNegatif / $totalSentimen) * 100, 1) : 0,
            'netral' => $totalSentimen > 0 ? round(($sentimenNetral / $totalSentimen) * 100, 1) : 0,
        ];
        $trendingIssues = collect($metricKognitifTerbaru?->trending_issues ?? [])->sortByDesc('pertumbuhan')->values();
        $jumlahIsuPrioritas = $trendingIssues->whereIn('tingkat', ['Kritis', 'Tinggi'])->count();
        $persentaseIsuPrioritas = $trendingIssues->isNotEmpty()
            ? round(($jumlahIsuPrioritas / $trendingIssues->count()) * 100)
            : 0;
        $statistikKognitif = [
            'labels' => $riwayatKognitif->map(fn (CognitiveMetric $metric): string => $metric->tanggal->format('d/m'))->all(),
            'positif' => $riwayatKognitif->pluck('sentimen_positif')->all(),
            'negatif' => $riwayatKognitif->pluck('sentimen_negatif')->all(),
            'netral' => $riwayatKognitif->pluck('sentimen_netral')->all(),
        ];

        return compact(
            'berita',
            'statistikTren',
            'metricKognitifTerbaru',
            'persentaseSentimen',
            'totalSentimen',
            'trendingIssues',
            'jumlahIsuPrioritas',
            'persentaseIsuPrioritas',
            'statistikKognitif',
        );
    }
}
