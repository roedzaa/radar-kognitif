<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan kolom koordinat geospasial.
     */
    public function up(): void
    {
        Schema::table('berita_osint', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('status_alert'); // Koordinat lintang geospasial
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');     // Koordinat bujur geospasial
        });
    }

    /**
     * Batalkan migrasi kolom koordinat.
     */
    public function down(): void
    {
        Schema::table('berita_osint', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
