<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Anggota grup dan admin grup
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete()->comment('Grup');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Anggota');
            $table->enum('member_role', ['admin', 'member'])->default('member')->comment('Admin grup atau anggota');
            $table->foreignId('added_by')->constrained('users')->cascadeOnDelete()->comment('Penambah');
            $table->timestamps();
            $table->unique(['group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
