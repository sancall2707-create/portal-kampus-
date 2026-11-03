<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penghitung nomor urut surat per unit, kategori, dan tahun
        Schema::create('letter_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete()->comment('Unit');
            $table->foreignId('category_id')->constrained('letter_categories')->restrictOnDelete()->comment('Kategori');
            $table->unsignedSmallInteger('year')->comment('Tahun');
            $table->unsignedInteger('last_number')->default(0)->comment('Nomor terakhir terpakai');
            $table->timestamps();
            $table->unique(['unit_id', 'category_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_sequences');
    }
};
