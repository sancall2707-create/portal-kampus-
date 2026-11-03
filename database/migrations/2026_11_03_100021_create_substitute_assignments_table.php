<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penunjukan guru pengganti (inval)
        Schema::create('substitute_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_request_id')->constrained('leave_requests')->cascadeOnDelete()->comment('Izin yang digantikan');
            $table->foreignId('substitute_user_id')->constrained('users')->cascadeOnDelete()->comment('Guru pengganti');
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete()->comment('Kepala Sekolah / pendelegasi');
            $table->string('class_subject_info', 255)->nullable()->comment('Kelas, mata pelajaran, jam');
            $table->text('instructions')->nullable()->comment('Instruksi / tugas KBM');
            $table->enum('status', ['assigned', 'acknowledged'])->default('assigned')->comment('Ditugaskan atau sudah dikonfirmasi');
            $table->dateTime('acknowledged_at')->nullable()->comment('Waktu konfirmasi');
            $table->timestamps();
            $table->index(['substitute_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('substitute_assignments');
    }
};
