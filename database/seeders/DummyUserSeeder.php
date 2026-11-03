<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use App\Models\UserUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder Akun Dummy untuk Pengujian SIAP PL Deltamas (Tahap Pengembangan).
 * Password seragam untuk semua akun pengujian: "password123".
 */
class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        $pgtk = Unit::where('code', 'PGTK')->firstOrFail();
        $sd   = Unit::where('code', 'SD')->firstOrFail();
        $smp  = Unit::where('code', 'SMP')->firstOrFail();
        $sma  = Unit::where('code', 'SMA')->firstOrFail();

        // ---------------------------------------------------------------------
        // 1. KOORDINATOR SEKOLAH (Super Admin Kampus)
        // ---------------------------------------------------------------------
        $koord = User::updateOrCreate(
            ['nip' => 'PL0001'],
            [
                'user_type'      => 'staff',
                'name'           => 'Br. Stefanus Ngadenin, FIC',
                'email'          => 'koordinator@pangudiluhur.sch.id',
                'password'       => $defaultPassword,
                'phone_wa'       => '081234567001',
                'birth_date'     => '1975-08-15',
                'nik'            => '3216000000000001',
                'job_title'      => 'Koordinator Sekolah PL Deltamas',
                'staff_category' => 'pimpinan',
                'status'         => 'active',
            ]
        );
        $koord->syncRoles(['koordinator']);

        // ---------------------------------------------------------------------
        // 2. KEPALA SEKOLAH (Admin Unit)
        // ---------------------------------------------------------------------
        $kepsekData = [
            [
                'nip'   => 'PL1001',
                'name'  => 'Maria Theresia Sulastri, S.Pd',
                'email' => 'kepsek.pgtk@pangudiluhur.sch.id',
                'title' => 'Kepala Sekolah PG-TK',
                'unit'  => $pgtk,
            ],
            [
                'nip'   => 'PL1002',
                'name'  => 'Antonius Joko Waluyo, S.Pd',
                'email' => 'kepsek.sd@pangudiluhur.sch.id',
                'title' => 'Kepala Sekolah SD',
                'unit'  => $sd,
            ],
            [
                'nip'   => 'PL1003',
                'name'  => 'Agustinus Tri Wahyudi, M.Pd',
                'email' => 'kepsek.smp@pangudiluhur.sch.id',
                'title' => 'Kepala Sekolah SMP',
                'unit'  => $smp,
            ],
            [
                'nip'   => 'PL1004',
                'name'  => 'Drs. Fransiscus Xaverius Hartono',
                'email' => 'kepsek.sma@pangudiluhur.sch.id',
                'title' => 'Kepala Sekolah SMA',
                'unit'  => $sma,
            ],
        ];

        foreach ($kepsekData as $kd) {
            $user = User::updateOrCreate(
                ['nip' => $kd['nip']],
                [
                    'user_type'      => 'staff',
                    'name'           => $kd['name'],
                    'email'          => $kd['email'],
                    'password'       => $defaultPassword,
                    'phone_wa'       => '0812345670' . substr($kd['nip'], -2),
                    'birth_date'     => '1980-05-20',
                    'nik'            => '321600000000' . substr($kd['nip'], -4),
                    'job_title'      => $kd['title'],
                    'staff_category' => 'pimpinan',
                    'status'         => 'active',
                ]
            );
            $user->syncRoles(['kepala_sekolah']);

            UserUnit::updateOrCreate(
                ['user_id' => $user->id, 'unit_id' => $kd['unit']->id],
                [
                    'position'   => 'kepala_sekolah',
                    'is_primary' => true,
                    'start_date' => '2026-07-01',
                    'note'       => 'Pengangkatan Kepala Sekolah periode 2026/2027',
                ]
            );
        }

        // ---------------------------------------------------------------------
        // 3. GURU UNIT & GURU MULTI-UNIT
        // ---------------------------------------------------------------------
        // 3a. Guru SD
        $guruSD = User::updateOrCreate(
            ['nip' => 'PL2001'],
            [
                'user_type'      => 'staff',
                'name'           => 'Bernadetha Rini Hastuti, S.Pd',
                'email'          => 'guru.sd@pangudiluhur.sch.id',
                'password'       => $defaultPassword,
                'phone_wa'       => '081234567101',
                'birth_date'     => '1990-03-12',
                'nik'            => '3216000000002001',
                'job_title'      => 'Guru Kelas SD',
                'staff_category' => 'guru',
                'status'         => 'active',
            ]
        );
        $guruSD->syncRoles(['guru']);
        UserUnit::updateOrCreate(
            ['user_id' => $guruSD->id, 'unit_id' => $sd->id],
            ['position' => 'guru', 'is_primary' => true, 'start_date' => '2026-07-01']
        );

        // 3b. Guru SMP & SMA (Multi-unit: tes skenario guru lintas unit)
        $guruMulti = User::updateOrCreate(
            ['nip' => 'PL2002'],
            [
                'user_type'      => 'staff',
                'name'           => 'Yohanes Batista Sandiwan, S.Kom',
                'email'          => 'guru.informatika@pangudiluhur.sch.id',
                'password'       => $defaultPassword,
                'phone_wa'       => '081234567102',
                'birth_date'     => '1995-07-27',
                'nik'            => '3216000000002002',
                'job_title'      => 'Guru Informatika (SMP & SMA)',
                'staff_category' => 'guru',
                'status'         => 'active',
            ]
        );
        $guruMulti->syncRoles(['guru']);
        UserUnit::updateOrCreate(
            ['user_id' => $guruMulti->id, 'unit_id' => $smp->id],
            ['position' => 'guru', 'is_primary' => true, 'start_date' => '2026-07-01', 'note' => 'Unit Utama']
        );
        UserUnit::updateOrCreate(
            ['user_id' => $guruMulti->id, 'unit_id' => $sma->id],
            ['position' => 'guru', 'is_primary' => false, 'start_date' => '2026-07-01', 'note' => 'Penugasan mengajar jam tambahan']
        );

        // 3c. Guru PG-TK
        $guruPGTK = User::updateOrCreate(
            ['nip' => 'PL2003'],
            [
                'user_type'      => 'staff',
                'name'           => 'Elisabeth Dewi Anggraini, S.Pd',
                'email'          => 'guru.pgtk@pangudiluhur.sch.id',
                'password'       => $defaultPassword,
                'phone_wa'       => '081234567103',
                'birth_date'     => '1994-11-08',
                'nik'            => '3216000000002003',
                'job_title'      => 'Guru Kelas TK B',
                'staff_category' => 'guru',
                'status'         => 'active',
            ]
        );
        $guruPGTK->syncRoles(['guru']);
        UserUnit::updateOrCreate(
            ['user_id' => $guruPGTK->id, 'unit_id' => $pgtk->id],
            ['position' => 'guru', 'is_primary' => true, 'start_date' => '2026-07-01']
        );

        // ---------------------------------------------------------------------
        // 4. TATA USAHA (TU Kampus & Unit)
        // ---------------------------------------------------------------------
        $tu = User::updateOrCreate(
            ['nip' => 'PL3001'],
            [
                'user_type'      => 'staff',
                'name'           => 'Theresia Ratna Juwita, A.Md',
                'email'          => 'tu.kampus@pangudiluhur.sch.id',
                'password'       => $defaultPassword,
                'phone_wa'       => '081234567201',
                'birth_date'     => '1992-09-14',
                'nik'            => '3216000000003001',
                'job_title'      => 'Staf Tata Usaha Kampus',
                'staff_category' => 'tata_usaha',
                'status'         => 'active',
            ]
        );
        $tu->syncRoles(['tata_usaha']);
        UserUnit::updateOrCreate(
            ['user_id' => $tu->id, 'unit_id' => $sd->id],
            ['position' => 'tata_usaha', 'is_primary' => true, 'start_date' => '2026-07-01']
        );

        // ---------------------------------------------------------------------
        // 5. SATPAM & PELAKSANA LAPANGAN
        // ---------------------------------------------------------------------
        // 5a. Satpam (Akses Buku Tamu, Buku Jaga, Panic Button)
        $satpam = User::updateOrCreate(
            ['nip' => 'PL4001'],
            [
                'user_type'      => 'staff',
                'name'           => 'Petrus Joko Santoso',
                'email'          => 'satpam.pos1@pangudiluhur.sch.id',
                'password'       => $defaultPassword,
                'phone_wa'       => '081234567301',
                'birth_date'     => '1987-04-18',
                'nik'            => '3216000000004001',
                'job_title'      => 'Komandan Regu Satpam (Pos Gerbang)',
                'staff_category' => 'satpam',
                'status'         => 'active',
            ]
        );
        $satpam->syncRoles(['satpam_pelaksana']);
        UserUnit::updateOrCreate(
            ['user_id' => $satpam->id, 'unit_id' => $sd->id],
            ['position' => 'satpam', 'is_primary' => true, 'start_date' => '2026-07-01']
        );

        // 5b. Pelaksana (Teknisi / Kebersihan: Direct Tasking & Tiket Kerusakan)
        $pelaksana = User::updateOrCreate(
            ['nip' => 'PL4002'],
            [
                'user_type'      => 'staff',
                'name'           => 'Ignatius Budi Prasetyo',
                'email'          => 'teknisi.sarpras@pangudiluhur.sch.id',
                'password'       => $defaultPassword,
                'phone_wa'       => '081234567302',
                'birth_date'     => '1989-12-05',
                'nik'            => '3216000000004002',
                'job_title'      => 'Pelaksana Sarpras & Teknisi',
                'staff_category' => 'pelaksana',
                'status'         => 'active',
            ]
        );
        $pelaksana->syncRoles(['satpam_pelaksana']);
        UserUnit::updateOrCreate(
            ['user_id' => $pelaksana->id, 'unit_id' => $sma->id],
            ['position' => 'pelaksana', 'is_primary' => true, 'start_date' => '2026-07-01']
        );

        // ---------------------------------------------------------------------
        // 6. PENGGUNA EKSTERNAL / UMUM (Simulasi Peminjam Fasilitas via Google SSO)
        // ---------------------------------------------------------------------
        $umum = User::updateOrCreate(
            ['email' => 'sekretariat.paroki@gmail.com'],
            [
                'user_type'         => 'external',
                'nip'               => null,
                'name'              => 'Ignatius Denny (Sie Kepemudaan Paroki)',
                'google_id'         => 'google_oauth_dummy_123456789',
                'password'          => null,
                'phone_wa'          => '081299990001',
                'birth_date'        => '1993-01-20',
                'nik'               => '3216000000009001',
                'job_title'         => null,
                'staff_category'    => null,
                'organization_name' => 'Paroki Ibu Teresa Cikarang',
                'status'            => 'active',
            ]
        );
        $umum->syncRoles(['umum']);
    }
}
