<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengajuan peminjaman fasilitas oleh staf maupun pihak eksternal
        Schema::create('facility_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->comment('Kode unik pengajuan');
            $table->foreignId('facility_id')->constrained('facilities')->restrictOnDelete()->comment('Fasilitas');
            $table->foreignId('requester_id')->constrained('users')->restrictOnDelete()->comment('Pemohon (staf atau eksternal)');
            $table->boolean('is_external')->default(false)->comment('1 = pemohon eksternal (label khusus)');
            $table->dateTime('start_at')->comment('Mulai pakai');
            $table->dateTime('end_at')->comment('Selesai pakai');
            $table->unsignedInteger('participants')->nullable()->comment('Jumlah peserta');
            $table->text('purpose')->comment('Rincian tujuan acara');
            $table->enum('status', ['pending_screening', 'pending_unit', 'pending_coordinator', 'approved', 'rejected', 'cancelled', 'completed'])->default('pending_screening')->comment('Tahap pengajuan');
            $table->string('rejection_reason', 255)->nullable()->comment('Alasan penolakan');
            $table->dateTime('approved_at')->nullable()->comment('Waktu persetujuan akhir');
            $table->string('eticket_token', 64)->nullable()->comment('Token tiket digital (QR) untuk Satpam');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['booking_code']);
            $table->unique(['eticket_token']);
            $table->index(['facility_id', 'start_at', 'end_at', 'status']);
            $table->index(['requester_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_bookings');
    }
};
