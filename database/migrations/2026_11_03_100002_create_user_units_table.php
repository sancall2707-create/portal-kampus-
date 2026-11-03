<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penempatan staf di unit beserta riwayat mutasi
        Schema::create('user_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->comment('Pegawai');
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete()->comment('Unit penempatan');
            $table->enum('position', ['kepala_sekolah', 'wakil_kepala', 'guru', 'tata_usaha', 'satpam', 'pelaksana'])->comment('Posisi di unit');
            $table->boolean('is_primary')->default(false)->comment('Unit utama (bila lebih dari satu)');
            $table->date('start_date')->comment('Mulai bertugas');
            $table->date('end_date')->nullable()->comment('Selesai bertugas (kosong = masih aktif)');
            $table->string('note', 255)->nullable()->comment('Catatan mutasi');
            $table->timestamps();
            $table->index(['user_id', 'end_date']);
            $table->index(['unit_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_units');
    }
};
