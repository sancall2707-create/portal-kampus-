<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catatan penggunaan Login As (simulasi akun)
        Schema::create('impersonation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('impersonator_id')->constrained('users')->restrictOnDelete()->comment('Koordinator');
            $table->foreignId('impersonated_id')->constrained('users')->restrictOnDelete()->comment('Akun yang disimulasikan');
            $table->string('reason', 255)->comment('Alasan (troubleshooting)');
            $table->dateTime('started_at')->comment('Mulai');
            $table->dateTime('ended_at')->nullable()->comment('Selesai');
            $table->string('ip_address', 45)->comment('Alamat IP');
            $table->index(['impersonator_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impersonation_logs');
    }
};
