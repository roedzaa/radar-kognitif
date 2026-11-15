<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cognitive_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->unsignedInteger('sentimen_positif');
            $table->unsignedInteger('sentimen_negatif');
            $table->unsignedInteger('sentimen_netral');
            $table->unsignedInteger('jumlah_bot_terdeteksi');
            $table->json('trending_issues');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cognitive_metrics');
    }
};
