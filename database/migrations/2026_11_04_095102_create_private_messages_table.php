<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject');
            $table->text('body');
            $table->string('category', 50)->default('arahan'); // arahan, instruksi, teguran, apresiasi, rahasia
            $table->boolean('is_confidential')->default(true);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('deleted_by_sender_at')->nullable();
            $table->timestamp('deleted_by_recipient_at')->nullable();
            $table->timestamps();

            $table->index(['recipient_id', 'read_at']);
            $table->index(['sender_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_messages');
    }
};
