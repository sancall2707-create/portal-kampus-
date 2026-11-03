<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Layar sapaan wajib (mandatory screen) dari Koordinator
        Schema::create('greetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->comment('Pembuat (Koordinator)');
            $table->string('title', 150)->comment('Judul sapaan');
            $table->text('message')->comment('Isi sapaan');
            $table->string('photo_path', 255)->nullable()->comment('Foto Koordinator');
            $table->dateTime('display_start')->comment('Mulai tayang');
            $table->dateTime('display_end')->comment('Akhir tayang');
            $table->string('cta_label', 50)->default('Masuk Beranda')->comment('Label tombol, default Masuk Beranda');
            $table->boolean('is_active')->default(true)->comment('Aktif');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_active', 'display_start', 'display_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('greetings');
    }
};
