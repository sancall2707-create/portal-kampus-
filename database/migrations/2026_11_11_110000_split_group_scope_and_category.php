<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rancangan menuntut dua atribut terpisah pada pengajuan grup:
     * "Nama Kepanitiaan" dan "Kategori" (jenis kepanitiaan), sementara
     * cakupan kampus/unit adalah dimensi lain.
     *
     * Skema awal hanya punya satu kolom `category` enum('campus','unit')
     * yang sebenarnya merepresentasikan cakupan. Migrasi ini memisahkan
     * keduanya: `scope` untuk cakupan, `category` untuk jenis kepanitiaan.
     */
    public function up(): void
    {
        // Pindahkan nilai cakupan lama ke kolom scope yang baru
        DB::statement("ALTER TABLE `groups` ADD COLUMN `scope` ENUM('campus','unit') NOT NULL DEFAULT 'unit' AFTER `name`");
        DB::statement("UPDATE `groups` SET `scope` = `category`");

        // Jadikan category menampung jenis kepanitiaan sesuai rancangan
        DB::statement("ALTER TABLE `groups` MODIFY COLUMN `category` ENUM('kepanitiaan','bidang_kerja','proyek','lainnya') NOT NULL DEFAULT 'kepanitiaan'");

        // Deskripsi dan alasan penolakan butuh ruang lebih panjang
        DB::statement("ALTER TABLE `groups` MODIFY COLUMN `description` VARCHAR(500) NULL");
        DB::statement("ALTER TABLE `groups` MODIFY COLUMN `reject_reason` VARCHAR(300) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `groups` MODIFY COLUMN `category` VARCHAR(20) NOT NULL");
        DB::statement("UPDATE `groups` SET `category` = `scope`");
        DB::statement("ALTER TABLE `groups` MODIFY COLUMN `category` ENUM('campus','unit') NOT NULL");
        DB::statement("ALTER TABLE `groups` DROP COLUMN `scope`");
        DB::statement("ALTER TABLE `groups` MODIFY COLUMN `description` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `groups` MODIFY COLUMN `reject_reason` VARCHAR(255) NULL");
    }
};
