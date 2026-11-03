<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jadwal blokir fasilitas (pemeliharaan atau acara internal)
        Schema::create('facility_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete()->comment('Fasilitas');
            $table->dateTime('start_at')->comment('Mulai blokir');
            $table->dateTime('end_at')->comment('Akhir blokir');
            $table->enum('reason', ['maintenance', 'internal_event', 'other'])->comment('Alasan');
            $table->string('note', 255)->nullable()->comment('Catatan');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->comment('Pembuat');
            $table->timestamps();
            $table->index(['facility_id', 'start_at', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_blocks');
    }
};
