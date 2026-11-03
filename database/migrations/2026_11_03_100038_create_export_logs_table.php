<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Riwayat unduhan laporan, leger, dan buku induk
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Pengunduh');
            $table->enum('export_type', ['rekap_presensi', 'rekap_izin', 'rekap_fasilitas', 'buku_tamu', 'lainnya'])->comment('Jenis');
            $table->json('params')->nullable()->comment('Filter yang dipakai');
            $table->string('file_path', 255)->nullable()->comment('Berkas hasil');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_logs');
    }
};
