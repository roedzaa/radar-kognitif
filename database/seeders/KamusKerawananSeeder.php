<?php

namespace Database\Seeders;

use App\Models\KamusKerawanan;
use Illuminate\Database\Seeder;

class KamusKerawananSeeder extends Seeder
{
    /**
     * Jalankan database seeder untuk tabel `kamus_kerawanan`.
     * Mengisi data awal kata kunci beserta klasifikasi tingkat bahayanya.
     */
    public function run(): void
    {
        // Daftar data awal kata kunci kerawanan
        $daftarKataKunci = [
            [
                'kata_kunci' => 'provokasi',
                'tingkat_bahaya' => 'Merah',
            ],
            [
                'kata_kunci' => 'makar',
                'tingkat_bahaya' => 'Merah',
            ],
            [
                'kata_kunci' => 'bentrok',
                'tingkat_bahaya' => 'Merah',
            ],
            [
                'kata_kunci' => 'hoaks',
                'tingkat_bahaya' => 'Kuning',
            ],
            [
                'kata_kunci' => 'demonstrasi',
                'tingkat_bahaya' => 'Kuning',
            ],
        ];

        // Menyimpan data ke tabel dengan proteksi agar tidak terjadi duplikasi saat seeder dijalankan ulang
        foreach ($daftarKataKunci as $item) {
            KamusKerawanan::updateOrCreate(
                ['kata_kunci' => $item['kata_kunci']],
                ['tingkat_bahaya' => $item['tingkat_bahaya']]
            );
        }
    }
}
