<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Siaran informasi (broadcast) tingkat kampus atau unit
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->restrictOnDelete()->comment('Pengirim');
            $table->enum('scope', ['campus', 'unit'])->comment('Tujuan informasi');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit tujuan bila scope = unit');
            $table->enum('category', ['new', 'reminder'])->comment('Pesan Baru atau Reminder');
            $table->string('body', 255)->comment('Isi pesan; batas 100 karakter diatur app_settings');
            $table->boolean('is_urgent')->default(false)->comment('Tandai penting');
            $table->dateTime('publish_at')->comment('Waktu tayang');
            $table->dateTime('display_until')->nullable()->comment('Batas waktu penayangan');
            $table->enum('status', ['draft', 'published', 'withdrawn'])->default('draft')->comment('Status');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['scope', 'unit_id', 'publish_at']);
            $table->index(['display_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
