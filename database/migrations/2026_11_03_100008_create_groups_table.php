<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Grup komunikasi dan kepanitiaan beserta status persetujuan
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->comment('Nama grup / kepanitiaan');
            $table->enum('category', ['campus', 'unit'])->comment('Skala grup');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit pemilik');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete()->comment('Pengaju (Guru)');
            $table->enum('status', ['pending', 'active', 'rejected', 'archived'])->default('pending')->comment('Aktif, Menunggu Persetujuan, atau Ditolak');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->comment('Kepala Sekolah yang memutuskan');
            $table->dateTime('reviewed_at')->nullable()->comment('Waktu keputusan');
            $table->string('reject_reason', 255)->nullable()->comment('Alasan penolakan');
            $table->string('description', 255)->nullable()->comment('Keterangan');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['unit_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
