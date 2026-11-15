<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi database.
     * Membuat tabel `berita_osint` untuk menyimpan artikel berita hasil penarikan OSINT.
     */
    public function up(): void
    {
        Schema::create('berita_osint', function (Blueprint $table) {
            $table->id(); // Primary Key auto-increment
            $table->text('judul'); // Judul berita OSINT
            $table->string('sumber', 100)->nullable(); // Sumber media atau portal berita (maksimal 100 karakter, opsional)
            $table->text('link')->nullable(); // Tautan langsung ke artikel berita (opsional)
            $table->dateTime('waktu_publish')->nullable(); // Waktu berita diterbitkan (opsional)
            $table->enum('status_alert', ['Aman', 'Kuning', 'Merah'])->default('Aman'); // Status ancaman/peringatan dini (default: 'Aman')
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Batalkan migrasi database.
     * Menghapus tabel `berita_osint` jika dilakukan rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_osint');
    }
};
