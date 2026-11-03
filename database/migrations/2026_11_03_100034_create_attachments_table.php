<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Metadata berkas unggahan (polimorfik) untuk seluruh modul
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type', 100)->comment('Nama tabel pemilik (leave_requests, facility_bookings, tasks, tickets, letters, facilities, greetings, shift_logs)');
            $table->unsignedBigInteger('attachable_id')->comment('ID pemilik');
            $table->string('purpose', 50)->comment('surat_dokter, surat_permohonan, foto_tugas, foto_tiket, foto_fasilitas, arsip_surat');
            $table->string('disk', 20)->comment('Disk penyimpanan');
            $table->string('path', 255)->comment('Lokasi berkas');
            $table->string('original_name', 255)->comment('Nama asli');
            $table->string('mime', 100)->comment('Tipe berkas');
            $table->unsignedBigInteger('size_bytes')->comment('Ukuran');
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete()->comment('Pengunggah');
            $table->timestamps();
            $table->index(['attachable_type', 'attachable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
