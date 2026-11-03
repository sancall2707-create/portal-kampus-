<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penerima dan status baca tiap siaran
        Schema::create('announcement_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete()->comment('Siaran');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Penerima');
            $table->dateTime('delivered_at')->nullable()->comment('Waktu terkirim');
            $table->dateTime('read_at')->nullable()->comment('Waktu dibaca (kosong = belum membaca)');
            $table->timestamps();
            $table->unique(['announcement_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_recipients');
    }
};
