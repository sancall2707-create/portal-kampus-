<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Register surat masuk dan keluar
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->enum('direction', ['in', 'out'])->comment('Surat masuk atau keluar');
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete()->comment('Unit pengelola');
            $table->foreignId('category_id')->constrained('letter_categories')->restrictOnDelete()->comment('Kategori');
            $table->string('letter_number', 100)->comment('Nomor surat');
            $table->date('letter_date')->comment('Tanggal surat');
            $table->string('subject', 255)->comment('Perihal');
            $table->string('party_name', 150)->comment('Pengirim (masuk) atau tujuan (keluar)');
            $table->enum('classification', ['biasa', 'penting', 'rahasia'])->default('biasa')->comment('Klasifikasi');
            $table->foreignId('registered_by')->constrained('users')->restrictOnDelete()->comment('Pencatat');
            $table->text('notes')->nullable()->comment('Catatan');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['unit_id', 'direction', 'letter_date']);
            $table->fullText(['subject', 'party_name']);
            $table->index(['letter_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
