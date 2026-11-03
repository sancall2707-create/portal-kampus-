<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Data referensi awal SIAP PL Deltamas.
 * PG-TK digabung menjadi satu unit (4 unit total).
 */
class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        foreach ([
            ['code' => 'PGTK', 'name' => 'PG-TK Pangudi Luhur Deltamas', 'sort_order' => 1],
            ['code' => 'SD',   'name' => 'SD Pangudi Luhur Deltamas',    'sort_order' => 2],
            ['code' => 'SMP',  'name' => 'SMP Pangudi Luhur Deltamas',   'sort_order' => 3],
            ['code' => 'SMA',  'name' => 'SMA Pangudi Luhur Deltamas',   'sort_order' => 4],
        ] as $u) {
            DB::table('units')->updateOrInsert(['code' => $u['code']], $u + ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
        }

        foreach ([
            ['code' => 'sakit', 'name' => 'Sakit', 'requires_attachment' => true],
            ['code' => 'izin',  'name' => 'Izin',  'requires_attachment' => false],
            ['code' => 'cuti',  'name' => 'Cuti',  'requires_attachment' => false],
        ] as $t) {
            DB::table('leave_types')->updateOrInsert(['code' => $t['code']], $t + ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
        }

        foreach (['Kendaraan', 'Ruang', 'Rohani', 'Peralatan'] as $name) {
            DB::table('facility_categories')->updateOrInsert(['name' => $name], ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]);
        }

        foreach ([
            ['broadcast_max_chars', '100', 'broadcast', 'Batas karakter isi broadcast'],
            ['agenda_max_chars', '100', 'agenda', 'Batas karakter rincian agenda'],
            ['greeting_max_highlights', '3', 'greeting', 'Jumlah maksimum highlight pada layar sapaan'],
            ['attachment_max_kb', '5120', 'attachment', 'Ukuran maksimum lampiran (KB)'],
            ['attachment_mimes', 'jpg,jpeg,png,pdf', 'attachment', 'Tipe berkas yang diizinkan'],
        ] as [$key, $value, $group, $desc]) {
            DB::table('app_settings')->updateOrInsert(['setting_key' => $key], [
                'setting_value' => $value, 'setting_group' => $group, 'description' => $desc,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $yearId = DB::table('academic_years')->where('name', '2026/2027')->value('id')
            ?? DB::table('academic_years')->insertGetId([
                'name' => '2026/2027', 'start_date' => '2026-07-13', 'end_date' => '2027-06-30',
                'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);

        foreach ([
            ['ganjil', '2026-07-13', '2026-12-31', true],
            ['genap',  '2027-01-04', '2027-06-30', false],
        ] as [$name, $start, $end, $active]) {
            DB::table('semesters')->updateOrInsert(['academic_year_id' => $yearId, 'name' => $name], [
                'start_date' => $start, 'end_date' => $end, 'is_active' => $active,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
