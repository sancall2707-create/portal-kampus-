<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penerima peringatan darurat dan konfirmasi terima
        Schema::create('panic_alert_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained('panic_alerts')->cascadeOnDelete()->comment('Peringatan');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Penerima (Koordinator, Kepala Sekolah, TU)');
            $table->dateTime('notified_at')->nullable()->comment('Waktu notifikasi dikirim');
            $table->dateTime('acknowledged_at')->nullable()->comment('Waktu dikonfirmasi diterima');
            $table->timestamps();
            $table->unique(['alert_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panic_alert_recipients');
    }
};
