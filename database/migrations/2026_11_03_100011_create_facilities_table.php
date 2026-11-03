<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master fasilitas dan aset yang dapat dipinjam
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->comment('Contoh Toyota Hiace, Aula SMA, Gua Maria Sendang Kahuripan, Kapel Bruderan');
            $table->foreignId('category_id')->constrained('facility_categories')->restrictOnDelete()->comment('Kategori');
            $table->enum('scope', ['campus', 'unit'])->comment('Skala fasilitas');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit pemilik bila scope = unit');
            $table->unsignedInteger('capacity')->nullable()->comment('Kapasitas maksimal');
            $table->text('description')->nullable()->comment('Deskripsi');
            $table->text('rules_text')->nullable()->comment('Tata tertib peminjaman bagi pihak luar');
            $table->enum('visibility', ['internal', 'public'])->comment('Hanya Internal atau Dapat Dipinjam Umum');
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active')->comment('Aktif, mode pemeliharaan, atau nonaktif');
            $table->string('maintenance_note', 255)->nullable()->comment('Alasan mode pemeliharaan');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->comment('Pembuat');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['scope', 'unit_id']);
            $table->index(['visibility', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
