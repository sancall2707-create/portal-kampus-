<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tahun ajaran
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->comment('Contoh 2026/2027');
            $table->date('start_date')->comment('Mulai');
            $table->date('end_date')->comment('Selesai');
            $table->boolean('is_active')->default(true)->comment('Hanya satu yang aktif');
            $table->timestamps();
            $table->unique(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
