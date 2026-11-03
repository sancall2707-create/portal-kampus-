<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jadwal mengajar pribadi guru
        Schema::create('teaching_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->restrictOnDelete()->comment('Guru');
            $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete()->comment('Semester');
            $table->string('class_label', 30)->nullable()->comment('Nama rombel (teks; ditautkan ke classrooms saat modul E-Raport dibangun)');
            $table->string('subject_label', 100)->nullable()->comment('Mata pelajaran (teks; ditautkan ke subjects saat modul E-Raport dibangun)');
            $table->unsignedTinyInteger('day_of_week')->comment('1 = Senin sampai 7 = Minggu');
            $table->time('start_time')->comment('Jam mulai');
            $table->time('end_time')->comment('Jam selesai');
            $table->string('room', 50)->nullable()->comment('Ruang');
            $table->timestamps();
            $table->index(['teacher_id', 'semester_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_schedules');
    }
};
