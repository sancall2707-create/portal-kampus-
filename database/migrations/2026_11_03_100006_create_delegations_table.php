<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Delegasi wewenang sementara (Plt) untuk persetujuan dan tugas akademik
        Schema::create('delegations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delegator_id')->constrained('users')->restrictOnDelete()->comment('Pemberi delegasi (Koordinator atau Kepala Sekolah)');
            $table->foreignId('delegate_id')->constrained('users')->restrictOnDelete()->comment('Penerima delegasi (Wakasek / guru senior / staf)');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit yang berlaku');
            $table->enum('scope', ['approval_leave', 'approval_facility', 'academic_assignment', 'academic_monitoring', 'all'])->comment('Cakupan wewenang');
            $table->dateTime('start_at')->comment('Mulai berlaku');
            $table->dateTime('end_at')->comment('Berakhir');
            $table->string('reason', 255)->nullable()->comment('Alasan (cuti, tugas luar, sakit)');
            $table->boolean('is_active')->default(true)->comment('Dapat dicabut sebelum waktunya');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->comment('Pembuat');
            $table->timestamps();
            $table->index(['delegate_id', 'is_active', 'start_at', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delegations');
    }
};
