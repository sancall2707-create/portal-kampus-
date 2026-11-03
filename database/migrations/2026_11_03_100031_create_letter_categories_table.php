<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kategori dan format penomoran surat
        Schema::create('letter_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->comment('Kode kategori');
            $table->string('name', 100)->comment('Nama kategori');
            $table->string('numbering_format', 100)->comment('Contoh {seq}/{code}/{unit}/{bulan_romawi}/{tahun}');
            $table->boolean('is_active')->default(true)->comment('Aktif');
            $table->timestamps();
            $table->unique(['code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_categories');
    }
};
