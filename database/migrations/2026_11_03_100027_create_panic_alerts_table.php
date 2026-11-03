<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Peringatan darurat (Panic Button) dari Satpam atau Pelaksana
        Schema::create('panic_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('triggered_by')->constrained('users')->restrictOnDelete()->comment('Penekan tombol');
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete()->comment('Unit lokasi');
            $table->string('location_text', 150)->nullable()->comment('Lokasi');
            $table->decimal('latitude', 10, 7)->nullable()->comment('Koordinat (opsional)');
            $table->decimal('longitude', 10, 7)->nullable()->comment('Koordinat (opsional)');
            $table->string('note', 255)->nullable()->comment('Catatan singkat');
            $table->dateTime('triggered_at')->comment('Waktu ditekan');
            $table->enum('status', ['active', 'acknowledged', 'resolved'])->default('active')->comment('Status penanganan');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete()->comment('Penutup');
            $table->dateTime('resolved_at')->nullable()->comment('Waktu ditutup');
            $table->string('resolution_note', 255)->nullable()->comment('Catatan penanganan');
            $table->timestamps();
            $table->index(['status', 'triggered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panic_alerts');
    }
};
