<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tiket pelaporan kerusakan fasilitas dan insiden lapangan
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no', 20)->comment('Nomor tiket');
            $table->foreignId('reporter_id')->constrained('users')->restrictOnDelete()->comment('Pelapor');
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete()->comment('Unit lokasi');
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->nullOnDelete()->comment('Fasilitas terkait');
            $table->string('location_text', 150)->nullable()->comment('Lokasi bebas (mis. Kelas 7A)');
            $table->enum('type', ['damage', 'cleanliness', 'security', 'other'])->comment('Jenis');
            $table->text('description')->comment('Uraian');
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium')->comment('Tingkat');
            $table->enum('status', ['open', 'assigned', 'in_progress', 'resolved', 'closed'])->default('open')->comment('Status');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()->comment('Teknisi / pelaksana');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete()->comment('Penugas');
            $table->dateTime('resolved_at')->nullable()->comment('Waktu selesai');
            $table->text('resolution_note')->nullable()->comment('Catatan penyelesaian');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['ticket_no']);
            $table->index(['unit_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
