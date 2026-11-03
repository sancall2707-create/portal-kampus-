<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengaturan sistem yang dapat diubah tanpa deploy
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->comment('Contoh broadcast_max_chars');
            $table->text('setting_value')->comment('Nilai');
            $table->string('setting_group', 50)->nullable()->comment('Pengelompokan');
            $table->string('description', 255)->nullable()->comment('Keterangan');
            $table->timestamps();
            $table->unique(['setting_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
