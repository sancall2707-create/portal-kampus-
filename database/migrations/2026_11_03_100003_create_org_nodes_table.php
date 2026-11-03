<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hierarki struktur organisasi kampus
        Schema::create('org_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('org_nodes')->nullOnDelete()->comment('Induk jabatan');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()->comment('Unit (kosong = tingkat kampus)');
            $table->string('title', 150)->comment('Nama jabatan / bagian');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Pemegang jabatan');
            $table->unsignedInteger('sort_order')->default(0)->comment('Urutan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('org_nodes');
    }
};
