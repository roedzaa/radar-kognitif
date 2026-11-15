<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi database.
     * Membuat tabel `kamus_kerawanan` untuk menyimpan daftar kata kunci kerawanan.
     */
    public function up(): void
    {
        Schema::create('kamus_kerawanan', function (Blueprint $table) {
            $table->id(); // Primary Key auto-increment
            $table->string('kata_kunci', 100); // Kata kunci pemantauan, maksimal 100 karakter
            $table->enum('tingkat_bahaya', ['Kuning', 'Merah'])->default('Kuning'); // Tingkat bahaya: 'Kuning' atau 'Merah' (default: 'Kuning')
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Batalkan migrasi database.
     * Menghapus tabel `kamus_kerawanan` jika dilakukan rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamus_kerawanan');
    }
};
