<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Informasi krusial yang ditampilkan pada layar sapaan
        Schema::create('greeting_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('greeting_id')->constrained('greetings')->cascadeOnDelete()->comment('Sapaan');
            $table->string('title', 150)->comment('Judul info (kunjungan yayasan, info lelayu, rapat penting)');
            $table->string('detail', 255)->nullable()->comment('Rincian singkat');
            $table->boolean('is_urgent')->default(false)->comment('Tandai urgent');
            $table->unsignedInteger('sort_order')->default(0)->comment('Urutan; jumlah maksimum diatur app_settings');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('greeting_highlights');
    }
};
