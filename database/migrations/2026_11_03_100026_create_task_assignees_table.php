<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penerima tugas dan konfirmasi penyelesaian
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete()->comment('Tugas');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Pelaksana (TU, Pelaksana, Satpam)');
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending')->comment('Status per orang');
            $table->dateTime('done_at')->nullable()->comment('Waktu selesai');
            $table->string('confirmation_note', 255)->nullable()->comment('Catatan konfirmasi');
            $table->timestamps();
            $table->unique(['task_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_assignees');
    }
};
