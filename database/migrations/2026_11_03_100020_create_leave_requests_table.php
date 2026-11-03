<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengajuan izin, sakit, dan cuti beserta status berjenjang
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Pemohon');
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete()->comment('Unit pemohon saat mengajukan');
            $table->foreignId('leave_type_id')->constrained('leave_types')->restrictOnDelete()->comment('Jenis');
            $table->date('start_date')->comment('Tanggal mulai');
            $table->date('end_date')->comment('Tanggal selesai');
            $table->unsignedSmallInteger('total_days')->comment('Jumlah hari');
            $table->text('reason')->nullable()->comment('Alasan');
            $table->text('substitute_plan')->nullable()->comment('Rencana KBM / tugas titipan untuk guru pengganti');
            $table->enum('status', ['pending_unit', 'pending_coordinator', 'approved', 'rejected', 'cancelled'])->default('pending_unit')->comment('Menunggu Kepala, menunggu Koordinator, disetujui, ditolak, dibatalkan');
            $table->dateTime('submitted_at')->comment('Waktu pengajuan');
            $table->dateTime('final_decided_at')->nullable()->comment('Waktu keputusan akhir');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'status']);
            $table->index(['unit_id', 'start_date', 'end_date']);
            $table->index(['status', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
