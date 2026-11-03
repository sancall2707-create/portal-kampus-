<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bukti pengguna sudah membaca sapaan wajib
        Schema::create('greeting_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('greeting_id')->constrained('greetings')->cascadeOnDelete()->comment('Sapaan');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Pembaca');
            $table->dateTime('read_at')->comment('Waktu baca');
            $table->unique(['greeting_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('greeting_reads');
    }
};
