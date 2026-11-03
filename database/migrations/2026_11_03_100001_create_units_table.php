<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Unit sekolah: PG-TK, SD, SMP, SMA
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->comment('Kode unit (PGTK, SD, SMP, SMA)');
            $table->string('name', 100)->comment('Nama unit');
            $table->boolean('is_active')->default(true)->comment('Default 1');
            $table->unsignedInteger('sort_order')->default(0)->comment('Urutan tampil');
            $table->timestamps();
            $table->unique(['code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
