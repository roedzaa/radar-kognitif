<?php

namespace App\Services;

use App\Models\BeritaOsint;
use App\Models\KamusKerawanan;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
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
        ['sumber' => 'Antara News - Teknologi', 'url' => 'https://www.antaranews.com/rss/tekno.xml'],
        ['sumber' => 'Antara News - Ekonomi', 'url' => 'https://www.antaranews.com/rss/ekonomi.xml'],
        ['sumber' => 'Antara News - Politik', 'url' => 'https://www.antaranews.com/rss/politik.xml'],
        ['sumber' => 'Antara News - Hukum', 'url' => 'https://www.antaranews.com/rss/hukum.xml'],
        ['sumber' => 'CNN Indonesia', 'url' => 'https://www.cnnindonesia.com/nasional/rss'],
        ['sumber' => 'CNN Indonesia - Teknologi', 'url' => 'https://www.cnnindonesia.com/teknologi/rss'],
        ['sumber' => 'CNN Indonesia - Internasional', 'url' => 'https://www.cnnindonesia.com/internasional/rss'],
        ['sumber' => 'CNN Indonesia - Ekonomi', 'url' => 'https://www.cnnindonesia.com/ekonomi/rss'],
        ['sumber' => 'CNBC Indonesia', 'url' => 'https://www.cnbcindonesia.com/news/rss'],
        ['sumber' => 'CNBC Indonesia - Teknologi', 'url' => 'https://www.cnbcindonesia.com/tech/rss'],
        ['sumber' => 'Tribun News', 'url' => 'https://www.tribunnews.com/rss'],
        ['sumber' => 'Detikcom', 'url' => 'https://news.detik.com/rss'],
        ['sumber' => 'Detik Inet', 'url' => 'https://inet.detik.com/rss'],
        ['sumber' => 'Okezone', 'url' => 'https://www.okezone.com/rss'],
        ['sumber' => 'SINDOnews', 'url' => 'https://www.sindonews.com/feed'],
        ['sumber' => 'SINDOnews Nasional', 'url' => 'https://nasional.sindonews.com/rss'],
        ['sumber' => 'Republika', 'url' => 'https://www.republika.co.id/rss'],
        ['sumber' => 'BBC News Indonesia', 'url' => 'https://feeds.bbci.co.uk/indonesia/rss.xml'],
        ['sumber' => 'Katadata', 'url' => 'https://katadata.co.id/rss'],
        ['sumber' => 'JPNN', 'url' => 'https://www.jpnn.com/rss'],
        ['sumber' => 'Media Indonesia', 'url' => 'https://mediaindonesia.com/feed/all'],
        ['sumber' => 'RRI', 'url' => 'https://rri.co.id/rss'],
        ['sumber' => 'Kementerian Pertahanan', 'url' => 'https://www.kemhan.go.id/feed'],
    ];

    /**
     * @return array{rss: int, total: int}
     */
    public function runFullScan(): array
    {
        $dictionary = KamusKerawanan::query()->get(['kata_kunci', 'tingkat_bahaya']);
        $rssCount = $this->scanRssChannels($dictionary);

        return [
            'rss' => $rssCount,
            'total' => $rssCount,
        ];
    }

    public function sendTestAlert(): void
    {
        $this->sendTelegramAlert(
            'UJI KIRIM: Sistem Peringatan Dini Komando Siber',
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
            $responses = Http::pool(function (Pool $pool): void {
                foreach (self::RSS_CHANNELS as $channel) {
                    $pool->as($channel['sumber'])
                        ->withoutVerifying()
                        ->withUserAgent(self::BROWSER_USER_AGENT)
                        ->connectTimeout(3)
                        ->timeout(6)
                        ->get($channel['url']);
                }
            }, 10);

            foreach (self::RSS_CHANNELS as $channel) {
                $response = $responses[$channel['sumber']] ?? null;

                if ($response instanceof Throwable) {
                    Log::warning('[MILITER EYES] Gagal memuat feed RSS.', [
                        'sumber' => $channel['sumber'],
                        'url' => $channel['url'],
                        'error' => $response->getMessage(),
                    ]);

                    continue;
                }

                if (! $response instanceof Response) {
                    Log::warning('[MILITER EYES] Respons feed RSS tidak tersedia.', [
                        'sumber' => $channel['sumber'],
                        'url' => $channel['url'],
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

    /**
     * @return array{latitude: float|null, longitude: float|null}
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
                return ['latitude' => $latitude, 'longitude' => $longitude];
            }
        }

        return ['latitude' => null, 'longitude' => null];
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
