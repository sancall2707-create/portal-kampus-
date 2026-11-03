<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Semester dalam tahun ajaran
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete()->comment('Tahun ajaran');
            $table->enum('name', ['ganjil', 'genap'])->comment('Semester');
            $table->date('start_date')->comment('Mulai');
            $table->date('end_date')->comment('Selesai');
            $table->boolean('is_active')->default(true)->comment('Hanya satu yang aktif');
            $table->timestamps();
            $table->unique(['academic_year_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
