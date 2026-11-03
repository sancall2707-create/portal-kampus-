<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buku jaga dan serah terima shift
        Schema::create('shift_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete()->comment('Unit');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Penulis');
            $table->enum('shift', ['pagi', 'siang', 'malam'])->comment('Shift');
            $table->date('log_date')->comment('Tanggal');
            $table->text('notes')->comment('Catatan serah terima');
            $table->foreignId('handed_over_to')->nullable()->constrained('users')->nullOnDelete()->comment('Penerima serah terima');
            $table->timestamps();
            $table->index(['unit_id', 'log_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_logs');
    }
};
