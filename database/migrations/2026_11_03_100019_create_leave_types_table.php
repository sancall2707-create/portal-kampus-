<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jenis ketidakhadiran
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->comment('sakit, izin, cuti');
            $table->string('name', 50)->comment('Nama jenis');
            $table->boolean('requires_attachment')->default(false)->comment('Wajib bukti (surat dokter)');
            $table->boolean('is_active')->default(true)->comment('Aktif');
            $table->timestamps();
            $table->unique(['code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
