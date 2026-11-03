<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Akun seluruh pengguna: staf internal (login NIP) dan pengguna umum eksternal (login Google)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['staff', 'external'])->comment('Staf internal atau pengguna umum (eksternal)');
            $table->string('nip', 30)->nullable()->comment('NIP / nomor pegawai, dipakai login staf');
            $table->string('name', 150)->comment('Nama lengkap');
            $table->string('email', 150)->nullable()->comment('Email; wajib untuk eksternal (akun Google)');
            $table->string('google_id', 64)->nullable()->comment('ID akun Google (SSO eksternal)');
            $table->string('password', 255)->nullable()->comment('Hash kata sandi; kosong untuk SSO');
            $table->string('phone_wa', 20)->nullable()->comment('Nomor WhatsApp aktif');
            $table->string('photo_path', 255)->nullable()->comment('Foto profil');
            $table->date('birth_date')->nullable()->comment('Tanggal lahir (info ulang tahun guru)');
            $table->string('nik', 255)->nullable()->comment('NIK untuk Digital ID; dienkripsi di level aplikasi');
            $table->string('job_title', 100)->nullable()->comment('Jabatan (tampil di Digital ID)');
            $table->enum('staff_category', ['pimpinan', 'guru', 'tata_usaha', 'satpam', 'pelaksana'])->nullable()->comment('Kategori kepegawaian');
            $table->string('organization_name', 150)->nullable()->comment('Instansi / kelompok (khusus eksternal)');
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active')->comment('Status akun');
            $table->dateTime('last_login_at')->nullable()->comment('Login terakhir (dasar pantauan pengguna aktif/pasif)');
            $table->string('remember_token', 100)->nullable()->comment('Token sesi');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['nip']);
            $table->unique(['email']);
            $table->unique(['google_id']);
            $table->index(['user_type', 'status']);
            $table->index(['last_login_at']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
