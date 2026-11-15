<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model KamusKerawanan
 *
 * Merepresentasikan tabel 'kamus_kerawanan' untuk mengelola kata kunci kerawanan
 * dan tingkat bahayanya pada aplikasi RADAR KOGNITIF.
 */
class KamusKerawanan extends Model
{
    use HasFactory;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'kamus_kerawanan';

    /**
     * Daftar atribut/kolom yang dapat diisi secara massal (mass assignment).
     *
     * @var list<string>
     */
    protected $fillable = [
        'kata_kunci',
        'tingkat_bahaya',
    ];
}
