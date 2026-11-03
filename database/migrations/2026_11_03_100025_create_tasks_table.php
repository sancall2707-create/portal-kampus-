<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penugasan lapangan langsung (Direct Tasking)
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->comment('Pemberi tugas');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit terkait');
            $table->string('title', 150)->comment('Judul tugas');
            $table->text('description')->nullable()->comment('Rincian tugas');
            $table->dateTime('due_at')->nullable()->comment('Tenggat');
            $table->enum('priority', ['normal', 'urgent'])->default('normal')->comment('Prioritas');
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending')->comment('Menunggu, Sedang Dikerjakan, Selesai');
            $table->foreignId('booking_id')->nullable()->constrained('facility_bookings')->nullOnDelete()->comment('Tugas terkait persiapan reservasi');
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete()->comment('Tugas terkait tiket kerusakan');
            $table->dateTime('completed_at')->nullable()->comment('Waktu selesai');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['unit_id', 'status']);
            $table->index(['due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
