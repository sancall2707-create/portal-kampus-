<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buku tamu digital di gerbang
        Schema::create('guest_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete()->comment('Unit tujuan');
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete()->comment('Satpam pencatat');
            $table->enum('guest_type', ['tamu', 'vendor', 'wali_murid', 'peminjam_eksternal'])->comment('Jenis tamu');
            $table->string('guest_name', 150)->comment('Nama');
            $table->string('institution', 150)->nullable()->comment('Instansi');
            $table->string('vehicle_plate', 15)->nullable()->comment('Plat kendaraan');
            $table->string('purpose', 255)->comment('Tujuan');
            $table->string('visit_to', 150)->nullable()->comment('Bertemu / menuju');
            $table->foreignId('booking_id')->nullable()->constrained('facility_bookings')->nullOnDelete()->comment('Terkait tiket reservasi');
            $table->dateTime('check_in_at')->comment('Masuk');
            $table->dateTime('check_out_at')->nullable()->comment('Keluar');
            $table->timestamps();
            $table->index(['unit_id', 'check_in_at']);
            $table->index(['vehicle_plate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_logs');
    }
};
