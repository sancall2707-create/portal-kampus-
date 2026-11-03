<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agenda kampus, unit, grup, dan pribadi
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->comment('Pembuat');
            $table->string('title', 150)->comment('Judul agenda');
            $table->enum('category', ['campus', 'unit', 'campus_group', 'unit_group', 'personal'])->comment('Kategori (menentukan audiens)');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit sasaran');
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete()->comment('Grup sasaran');
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->nullOnDelete()->comment('Ruang / fasilitas yang dipakai');
            $table->string('location_text', 150)->nullable()->comment('Lokasi teks bebas');
            $table->dateTime('start_at')->comment('Mulai');
            $table->dateTime('end_at')->comment('Selesai');
            $table->string('description', 255)->nullable()->comment('Rincian; batas 100 karakter diatur app_settings');
            $table->boolean('blocks_booking')->default(false)->comment('1 = memblokir reservasi (KBM, acara kampus)');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['start_at', 'end_at']);
            $table->index(['category', 'unit_id']);
            $table->index(['facility_id', 'start_at', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
