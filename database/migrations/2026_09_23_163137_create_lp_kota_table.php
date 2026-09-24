<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lp_kota', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_kota');
            // Satu kota bisa punya banyak kecamatan, jadi pasangan kota+kecamatan
            // yang dijaga unik — bukan nama kecamatan saja (nama kecamatan bisa
            // sama di dua kota berbeda).
            $table->string('nama_kecamatan');
            $table->text('deskripsi')->nullable();
            $table->string('gambar_utama')->nullable();
            $table->string('gambar_icon')->nullable();
            $table->timestamps();

            $table->unique(['nama_kota', 'nama_kecamatan'], 'lp_kota_wilayah_unique');
            $table->index('nama_kota');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lp_kota');
    }
};
