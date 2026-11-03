<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Riwayat login untuk pantauan pengguna aktif dan pasif serta keamanan
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Pengguna (kosong bila gagal dan tidak dikenal)');
            $table->string('identifier', 150)->comment('NIP / email yang dicoba');
            $table->enum('method', ['nip', 'google', 'impersonation'])->comment('Metode');
            $table->boolean('success')->default(false)->comment('Berhasil atau gagal');
            $table->string('ip_address', 45)->comment('Alamat IP');
            $table->string('user_agent', 255)->nullable()->comment('Perangkat');
            $table->dateTime('logged_in_at')->comment('Waktu');
            $table->index(['user_id', 'logged_in_at']);
            $table->index(['identifier', 'success', 'logged_in_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
