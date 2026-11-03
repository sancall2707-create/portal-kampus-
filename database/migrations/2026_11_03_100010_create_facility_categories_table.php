<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kategori fasilitas (kendaraan, ruang, rohani, peralatan)
        Schema::create('facility_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Nama kategori');
            $table->boolean('is_active')->default(true)->comment('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_categories');
    }
};
