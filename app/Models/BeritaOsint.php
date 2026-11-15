<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model BeritaOsint
 *
 * Merepresentasikan tabel 'berita_osint' untuk mengelola data artikel berita
 * hasil penarikan intelijen sumber terbuka (OSINT) pada aplikasi RADAR KOGNITIF.
 */
class BeritaOsint extends Model
{
    use HasFactory;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'berita_osint';

    /**
     * Daftar atribut/kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var list<string>
     */
    protected $fillable = [
        'judul',
        'sumber',
        'link',
        'waktu_publish',
        'status_alert',
        'latitude',
        'longitude',
    ];

    /**
     * Definisi konversi tipe data (type casting) untuk atribut model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu_publish' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
}
