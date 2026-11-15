<?php

namespace App\Services;

use App\Models\BeritaOsint;
use App\Models\KamusKerawanan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OsintScraperService
{
    private const BROWSER_USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /** @var list<array{sumber: string, url: string}> */
    private const RSS_CHANNELS = [
        ['sumber' => 'Google News - Cybersecurity Indonesia', 'url' => 'https://news.google.com/rss/search?q=cybersecurity%20indonesia&hl=id&gl=ID&ceid=ID:id'],
        ['sumber' => 'Google News - Serangan Siber TNI', 'url' => 'https://news.google.com/rss/search?q=serangan%20siber%20tni&hl=id&gl=ID&ceid=ID:id'],
        ['sumber' => 'Antara News', 'url' => 'https://www.antaranews.com/rss/terkini.xml'],
        ['sumber' => 'CNN Indonesia', 'url' => 'https://www.cnnindonesia.com/nasional/rss'],
        ['sumber' => 'CNBC Indonesia', 'url' => 'https://www.cnbcindonesia.com/news/rss'],
        ['sumber' => 'Tribun News', 'url' => 'https://www.tribunnews.com/rss'],
    ];

    /** @var list<array{tag: string, sumber: string}> */
    private const SOCIAL_PLATFORMS = [
        ['tag' => 'X/TWITTER', 'sumber' => 'X/Twitter Intelligence'],
        ['tag' => 'TIKTOK', 'sumber' => 'TikTok Viral Graph'],
        ['tag' => 'INSTAGRAM', 'sumber' => 'IG Dark Web'],
        ['tag' => 'FACEBOOK', 'sumber' => 'Facebook Influence Monitor'],
        ['tag' => 'THREADS', 'sumber' => 'Threads Narrative Mesh'],
    ];

    /** @var list<string> */
    private const SOCIAL_THEMES = [
        'Lonjakan percakapan tentang serangan ransomware nasional',
        'Narasi kebocoran data pemerintah mengalami eskalasi viral',
        'Kluster akun anonim menyebarkan hoaks keamanan siber',
        'Indikasi botnet mengamplifikasi isu serangan infrastruktur digital',
        'Konten deepfake pejabat publik menyebar lintas platform',
        'Percakapan mengenai peretasan sistem pertahanan meningkat tajam',
        'Kampanye disinformasi terkoordinasi menargetkan kepercayaan publik',
        'Tautan phishing bertema layanan pemerintah terdeteksi viral',
        'Akun buzzer mendorong provokasi terkait insiden keamanan nasional',
        'Eksploitasi zero-day menjadi topik dominan komunitas siber',
    ];

    /** @var list<array{nama: string, latitude: float, longitude: float}> */
    private const INDONESIA_REGIONS = [
        ['nama' => 'Sabang, Aceh', 'latitude' => 5.8942, 'longitude' => 95.3222],
        ['nama' => 'Medan, Sumatera Utara', 'latitude' => 3.5952, 'longitude' => 98.6722],
        ['nama' => 'Padang, Sumatera Barat', 'latitude' => -0.9471, 'longitude' => 100.4172],
        ['nama' => 'Pekanbaru, Riau', 'latitude' => 0.5071, 'longitude' => 101.4478],
        ['nama' => 'Tanjung Pinang, Kepulauan Riau', 'latitude' => 0.9186, 'longitude' => 104.4665],
        ['nama' => 'Jambi', 'latitude' => -1.6101, 'longitude' => 103.6131],
        ['nama' => 'Palembang, Sumatera Selatan', 'latitude' => -2.9761, 'longitude' => 104.7754],
        ['nama' => 'Bengkulu', 'latitude' => -3.7928, 'longitude' => 102.2608],
        ['nama' => 'Bandar Lampung', 'latitude' => -5.3971, 'longitude' => 105.2668],
        ['nama' => 'Pangkal Pinang, Bangka Belitung', 'latitude' => -2.1316, 'longitude' => 106.1169],
        ['nama' => 'Jakarta', 'latitude' => -6.2088, 'longitude' => 106.8456],
        ['nama' => 'Bandung, Jawa Barat', 'latitude' => -6.9175, 'longitude' => 107.6191],
        ['nama' => 'Semarang, Jawa Tengah', 'latitude' => -6.9667, 'longitude' => 110.4167],
        ['nama' => 'Yogyakarta', 'latitude' => -7.7956, 'longitude' => 110.3695],
        ['nama' => 'Surabaya, Jawa Timur', 'latitude' => -7.2575, 'longitude' => 112.7521],
        ['nama' => 'Serang, Banten', 'latitude' => -6.1201, 'longitude' => 106.1503],
        ['nama' => 'Denpasar, Bali', 'latitude' => -8.6705, 'longitude' => 115.2126],
        ['nama' => 'Mataram, Nusa Tenggara Barat', 'latitude' => -8.5833, 'longitude' => 116.1167],
        ['nama' => 'Kupang, Nusa Tenggara Timur', 'latitude' => -10.1772, 'longitude' => 123.6070],
        ['nama' => 'Pontianak, Kalimantan Barat', 'latitude' => -0.0263, 'longitude' => 109.3425],
        ['nama' => 'Palangka Raya, Kalimantan Tengah', 'latitude' => -2.2161, 'longitude' => 113.9137],
        ['nama' => 'Banjarmasin, Kalimantan Selatan', 'latitude' => -3.3194, 'longitude' => 114.5908],
        ['nama' => 'Samarinda, Kalimantan Timur', 'latitude' => -0.5022, 'longitude' => 117.1536],
        ['nama' => 'Tanjung Selor, Kalimantan Utara', 'latitude' => 2.8375, 'longitude' => 117.3653],
        ['nama' => 'Manado, Sulawesi Utara', 'latitude' => 1.4748, 'longitude' => 124.8421],
        ['nama' => 'Gorontalo', 'latitude' => 0.5435, 'longitude' => 123.0568],
        ['nama' => 'Palu, Sulawesi Tengah', 'latitude' => -0.9003, 'longitude' => 119.8779],
        ['nama' => 'Mamuju, Sulawesi Barat', 'latitude' => -2.6774, 'longitude' => 118.8867],
        ['nama' => 'Makassar, Sulawesi Selatan', 'latitude' => -5.1477, 'longitude' => 119.4327],
        ['nama' => 'Kendari, Sulawesi Tenggara', 'latitude' => -3.9985, 'longitude' => 122.5120],
        ['nama' => 'Ambon, Maluku', 'latitude' => -3.6547, 'longitude' => 128.1906],
        ['nama' => 'Sofifi, Maluku Utara', 'latitude' => 0.7373, 'longitude' => 127.5588],
        ['nama' => 'Manokwari, Papua Barat', 'latitude' => -0.8615, 'longitude' => 134.0620],
        ['nama' => 'Sorong, Papua Barat Daya', 'latitude' => -0.8762, 'longitude' => 131.2558],
        ['nama' => 'Jayapura, Papua', 'latitude' => -2.5337, 'longitude' => 140.7181],
        ['nama' => 'Nabire, Papua Tengah', 'latitude' => -3.3636, 'longitude' => 135.5018],
        ['nama' => 'Wamena, Papua Pegunungan', 'latitude' => -4.0959, 'longitude' => 138.9467],
        ['nama' => 'Merauke, Papua Selatan', 'latitude' => -8.4991, 'longitude' => 140.4011],
    ];

    /**
     * @return array{rss: int, social: int, total: int}
     */
    public function runFullScan(): array
    {
        $dictionary = KamusKerawanan::query()->get(['kata_kunci', 'tingkat_bahaya']);
        $rssCount = $this->scanRssChannels($dictionary);
        $socialCount = $this->generateSyntheticSocialSignals(12);

        return [
            'rss' => $rssCount,
            'social' => $socialCount,
            'total' => $rssCount + $socialCount,
        ];
    }

    public function sendTestAlert(): void
    {
        $this->sendTelegramAlert(
            'SIMULASI DARURAT: Uji Coba Sistem Peringatan Dini Komando Siber',
            'https://tni.mil.id',
            now(),
        );
    }

    /**
     * @param  Collection<int, KamusKerawanan>  $dictionary
     */
    private function scanRssChannels(Collection $dictionary): int
    {
        $previousLibxmlSetting = libxml_use_internal_errors(true);
        $processed = 0;

        try {
            foreach (self::RSS_CHANNELS as $channel) {
                try {
                    $response = Http::withoutVerifying()
                        ->withUserAgent(self::BROWSER_USER_AGENT)
                        ->timeout(10)
                        ->get($channel['url']);
                } catch (Throwable $exception) {
                    Log::warning('[MILITER EYES] Gagal memuat feed RSS.', [
                        'sumber' => $channel['sumber'],
                        'url' => $channel['url'],
                        'error' => $exception->getMessage(),
                    ]);

                    continue;
                }

                if ($response->failed()) {
                    Log::warning('[MILITER EYES] Feed RSS mengembalikan respons gagal.', [
                        'sumber' => $channel['sumber'],
                        'url' => $channel['url'],
                        'status' => $response->status(),
                    ]);

                    continue;
                }

                $rss = simplexml_load_string($response->body());

                if ($rss === false || ! isset($rss->channel->item)) {
                    Log::warning('[MILITER EYES] Format XML tidak valid.', [
                        'sumber' => $channel['sumber'],
                        'url' => $channel['url'],
                    ]);
                    libxml_clear_errors();

                    continue;
                }

                foreach ($rss->channel->item as $item) {
                    $title = trim((string) $item->title);
                    $link = trim((string) $item->link);

                    if ($title === '' || $link === '') {
                        continue;
                    }

                    $publishedAt = $this->parsePublishedAt((string) ($item->pubDate ?? ''), $channel['sumber'], $title);
                    $alertStatus = $this->detectAlertStatus($title, $dictionary);
                    $coordinates = $this->detectCoordinates($title);
                    $report = BeritaOsint::updateOrCreate(
                        ['judul' => $title],
                        [
                            'sumber' => $channel['sumber'],
                            'link' => $link,
                            'waktu_publish' => $publishedAt,
                            'status_alert' => $alertStatus,
                            'latitude' => $coordinates['latitude'],
                            'longitude' => $coordinates['longitude'],
                        ],
                    );

                    $processed++;

                    if ($report->wasRecentlyCreated && $alertStatus === 'Merah') {
                        $this->sendTelegramAlert($title, $link, $publishedAt);
                    }
                }
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousLibxmlSetting);
        }

        return $processed;
    }

    private function parsePublishedAt(string $value, string $source, string $title): ?Carbon
    {
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable $exception) {
            Log::notice('[MILITER EYES] Waktu publikasi RSS tidak dapat dibaca.', [
                'sumber' => $source,
                'judul' => $title,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  Collection<int, KamusKerawanan>  $dictionary
     */
    private function detectAlertStatus(string $title, Collection $dictionary): string
    {
        $status = 'Aman';

        foreach ($dictionary as $keyword) {
            if (stripos($title, $keyword->kata_kunci) === false) {
                continue;
            }

            if ($keyword->tingkat_bahaya === 'Merah') {
                return 'Merah';
            }

            if ($keyword->tingkat_bahaya === 'Kuning') {
                $status = 'Kuning';
            }
        }

        return $status;
    }

    private function generateSyntheticSocialSignals(int $count): int
    {
        $statuses = ['Merah', 'Merah', 'Kuning', 'Kuning', 'Aman'];

        for ($index = 0; $index < $count; $index++) {
            $platform = self::SOCIAL_PLATFORMS[$index % count(self::SOCIAL_PLATFORMS)];
            $theme = self::SOCIAL_THEMES[array_rand(self::SOCIAL_THEMES)];
            $region = self::INDONESIA_REGIONS[array_rand(self::INDONESIA_REGIONS)];
            $status = $statuses[array_rand($statuses)];
            $signalCode = strtoupper(bin2hex(random_bytes(4)));
            $title = "[SIMULASI {$platform['tag']}] {$theme} di {$region['nama']} // {$signalCode}";
            $coordinates = $this->spreadCoordinates(
                $region['latitude'],
                $region['longitude'],
                $title,
                0.18,
            );

            BeritaOsint::create([
                'judul' => $title,
                'sumber' => $platform['sumber'],
                'link' => null,
                'waktu_publish' => now()->subMinutes(random_int(1, 180)),
                'status_alert' => $status,
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude'],
            ]);
        }

        return $count;
    }

    /**
     * @return array{latitude: float, longitude: float}
     */
    private function detectCoordinates(string $title): array
    {
        $strategicLocations = [
            'jakarta' => [-6.2088, 106.8456], 'dki' => [-6.2088, 106.8456],
            'ikn' => [-0.9734, 116.7088], 'nusantara' => [-0.9734, 116.7088],
            'surabaya' => [-7.2575, 112.7521], 'jatim' => [-7.5360, 112.2384],
            'bandung' => [-6.9175, 107.6191], 'jabar' => [-6.9200, 107.6000],
            'semarang' => [-6.9667, 110.4167], 'jateng' => [-7.1509, 110.1402],
            'yogyakarta' => [-7.7956, 110.3695], 'jogja' => [-7.7956, 110.3695],
            'medan' => [3.5952, 98.6722], 'sumut' => [2.1154, 99.5451],
            'aceh' => [5.5483, 95.3238], 'sabang' => [5.8942, 95.3222],
            'padang' => [-0.9471, 100.4172], 'sumbar' => [-0.7399, 100.8000],
            'pekanbaru' => [0.5071, 101.4478], 'riau' => [0.2933, 101.7068],
            'batam' => [1.1301, 104.0529], 'kepri' => [3.9456, 108.1428],
            'natuna' => [3.9456, 108.1428], 'palembang' => [-2.9761, 104.7754],
            'sumsel' => [-3.3194, 104.9147], 'lampung' => [-5.4292, 105.2625],
            'pontianak' => [-0.0263, 109.3425], 'kalbar' => [-0.2787, 111.4753],
            'banjarmasin' => [-3.3194, 114.5908], 'kalsel' => [-3.0926, 115.2838],
            'balikpapan' => [-1.2379, 116.8529], 'samarinda' => [-0.5022, 117.1536],
            'kaltim' => [0.5387, 116.4194], 'makassar' => [-5.1477, 119.4327],
            'sulsel' => [-3.6687, 119.9740], 'manado' => [1.4748, 124.8421],
            'sulut' => [0.6247, 123.9750], 'palu' => [-0.9003, 119.8779],
            'sulteng' => [-1.4300, 121.4456], 'bali' => [-8.4095, 115.1889],
            'denpasar' => [-8.6705, 115.2126], 'lombok' => [-8.5799, 116.0999],
            'mataram' => [-8.5799, 116.0999], 'ntb' => [-8.6529, 117.3616],
            'kupang' => [-10.1772, 123.6070], 'ntt' => [-8.6574, 121.0794],
            'ambon' => [-3.6547, 128.1906], 'maluku' => [-3.2385, 130.1453],
            'papua' => [-4.2699, 138.0803], 'jayapura' => [-2.5337, 140.7181],
            'merauke' => [-8.4991, 140.4011], 'timika' => [-4.5467, 136.8839],
            'mimika' => [-4.5467, 136.8839],
        ];

        foreach ($strategicLocations as $name => [$latitude, $longitude]) {
            $pattern = '/(?<![\pL\pN])'.preg_quote($name, '/').'(?![\pL\pN])/iu';

            if (preg_match($pattern, $title) === 1) {
                return $this->spreadCoordinates($latitude, $longitude, $title, 0.025);
            }
        }

        $hash = unpack('Nindex', substr(hash('sha256', $title, true), 0, 4));
        $region = self::INDONESIA_REGIONS[$hash['index'] % count(self::INDONESIA_REGIONS)];

        return $this->spreadCoordinates($region['latitude'], $region['longitude'], $title, 0.35);
    }

    /**
     * @return array{latitude: float, longitude: float}
     */
    private function spreadCoordinates(float $latitude, float $longitude, string $title, float $radius): array
    {
        $hash = unpack('Nlatitude/Nlongitude', substr(hash('sha256', 'coordinate:'.$title, true), 0, 8));
        $scale = 4_294_967_295;

        return [
            'latitude' => round($latitude + (($hash['latitude'] / $scale) * 2 - 1) * $radius, 6),
            'longitude' => round($longitude + (($hash['longitude'] / $scale) * 2 - 1) * $radius, 6),
        ];
    }

    private function sendTelegramAlert(string $title, string $link, Carbon|string|null $publishedAt = null): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (empty($token) || empty($chatId)) {
            Log::warning('[MILITER EYES] Notifikasi Telegram dilewati: konfigurasi belum lengkap.');

            return;
        }

        $publishedText = $publishedAt instanceof Carbon
            ? $publishedAt->format('d/m/Y H:i:s').' WIB'
            : ($publishedAt ?: now()->format('d/m/Y H:i:s').' WIB');
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $safeLink = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
        $message = "<b>[PERINGATAN KRITIS: STATUS MERAH]</b>\n"
            ."Pusat Komando mendeteksi anomali informasi berisiko tinggi.\n\n"
            ."<b>Laporan:</b> {$safeTitle}\n"
            ."<b>Sumber:</b> <a href=\"{$safeLink}\">Akses Dokumen</a>\n"
            ."<b>Waktu Deteksi:</b> {$publishedText}\n\n"
            .'<i>Sistem EWS Otomatis - MILITER EYES TNI AD</i>';

        try {
            $response = Http::withoutVerifying()
                ->connectTimeout(5)
                ->timeout(10)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => false,
                ]);

            if ($response->failed()) {
                Log::error('[MILITER EYES] Telegram API error.', ['status' => $response->status()]);
            }
        } catch (Throwable $exception) {
            Log::error('[MILITER EYES] Exception Telegram.', ['error' => $exception->getMessage()]);
        }
    }
}
