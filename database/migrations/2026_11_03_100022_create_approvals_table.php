<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Langkah persetujuan berjenjang (polimorfik) untuk izin dan peminjaman fasilitas
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type', 100)->comment('leave_requests atau facility_bookings');
            $table->unsignedBigInteger('approvable_id')->comment('ID dokumen yang disetujui');
            $table->unsignedTinyInteger('step_order')->comment('Urutan langkah');
            $table->enum('step_role', ['tu_screening', 'unit_head', 'coordinator'])->comment('Peran penyetuju pada langkah ini');
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete()->comment('Pengambil keputusan sebenarnya');
            $table->foreignId('via_delegation_id')->nullable()->constrained('delegations')->nullOnDelete()->comment('Diisi bila keputusan lewat delegasi (Plt)');
            $table->enum('status', ['pending', 'approved', 'rejected', 'skipped'])->default('pending')->comment('Status langkah');
            $table->string('note', 255)->nullable()->comment('Catatan / alasan penolakan');
            $table->dateTime('decided_at')->nullable()->comment('Waktu keputusan');
            $table->timestamps();
            $table->index(['approvable_type', 'approvable_id', 'step_order']);
            $table->index(['step_role', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
