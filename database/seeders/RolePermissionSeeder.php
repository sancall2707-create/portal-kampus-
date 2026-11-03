<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Peran & izin Tahap Awal SIAP PL Deltamas.
 * Izin erapor.* sengaja belum dibuat (modul E-Raport masuk tahap pengembangan).
 * Tanda V* (batas unit/kondisi) ditegakkan di Policy, bukan di sini.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'user.manage' => 'Tambah, ubah, suspend, hapus akun; reset password',
            'user.impersonate' => 'Login As untuk troubleshooting, wajib alasan dan tercatat',
            'user.activity.view' => 'Pantau pengguna aktif dan pasif',
            'audit.view' => 'Lihat log aktivitas pimpinan unit',
            'masterdata.manage' => 'Unit, struktur organisasi, penempatan, tahun ajaran',
            'delegation.manage' => 'Beri delegasi (Plt) wewenang persetujuan',
            'greeting.manage' => 'Buat sapaan intersepsi dan lihat keterbacaan',
            'announcement.create.campus' => 'Siaran informasi kampus',
            'announcement.create.unit' => 'Siaran informasi unit',
            'announcement.readreceipt.view' => 'Daftar sudah dan belum membaca',
            'announcement.view' => 'Membaca pengumuman kampus dan unit',
            'agenda.create' => 'Buat agenda kampus / unit / pribadi',
            'agenda.view.all' => 'Lihat seluruh kalender',
            'group.request' => 'Ajukan grup / kepanitiaan',
            'group.approve' => 'Setujui atau tolak pembentukan grup',
            'group.supervise' => 'Pantau aktivitas grup',
            'leave.request' => 'Ajukan izin / sakit / cuti',
            'leave.validate.unit' => 'Validasi izin staf unit',
            'leave.approve.final' => 'Pengesahan akhir izin',
            'leave.monitor.all' => 'Monitor ketidakhadiran lintas unit',
            'facility.manage' => 'Kelola master fasilitas dan maintenance mode',
            'facility.book' => 'Ajukan peminjaman fasilitas',
            'facility.book.public' => 'Ajukan peminjaman fasilitas publik',
            'facility.screen.external' => 'Screening dokumen pengajuan eksternal',
            'facility.approve.unit' => 'Validasi peminjaman fasilitas unit',
            'facility.approve.campus' => 'Persetujuan akhir peminjaman',
            'facility.schedule.view' => 'Lihat jadwal reservasi untuk persiapan lapangan',
            'task.assign' => 'Beri tugas lapangan',
            'task.receive' => 'Terima dan konfirmasi tugas',
            'ticket.report' => 'Lapor kerusakan / insiden',
            'ticket.manage' => 'Terima dan tugaskan perbaikan',
            'panic.trigger' => 'Tekan Panic Button',
            'panic.receive' => 'Terima peringatan darurat',
            'guestbook.record' => 'Catat tamu di gerbang',
            'guestbook.view' => 'Lihat dan unduh buku tamu',
            'shiftlog.write' => 'Tulis buku jaga',
            'shiftlog.read' => 'Baca buku jaga',
            'letter.manage' => 'Surat masuk/keluar dan arsip digital',
            'report.admin.download' => 'Unduh rekap administratif',
            'dashboard.analytics' => 'Dashboard grafik',
            'profile.view.own' => 'Digital ID dan jadwal mengajar sendiri',
        ];

        foreach ($permissions as $name => $desc) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roles = [
            'koordinator' => [
                'user.manage',
                'user.impersonate',
                'user.activity.view',
                'audit.view',
                'masterdata.manage',
                'delegation.manage',
                'greeting.manage',
                'announcement.create.campus',
                'announcement.create.unit',
                'announcement.readreceipt.view',
                'announcement.view',
                'agenda.create',
                'agenda.view.all',
                'group.supervise',
                'leave.approve.final',
                'leave.monitor.all',
                'facility.manage',
                'facility.book',
                'facility.approve.campus',
                'facility.schedule.view',
                'task.assign',
                'ticket.manage',
                'panic.receive',
                'guestbook.view',
                'shiftlog.read',
                'letter.manage',
                'report.admin.download',
                'dashboard.analytics',
                'profile.view.own',
            ],
            'kepala_sekolah' => [
                'delegation.manage',
                'announcement.create.campus',
                'announcement.create.unit',
                'announcement.readreceipt.view',
                'announcement.view',
                'agenda.create',
                'agenda.view.all',
                'group.approve',
                'group.supervise',
                'leave.request',
                'leave.validate.unit',
                'leave.monitor.all',
                'facility.manage',
                'facility.book',
                'facility.approve.unit',
                'facility.schedule.view',
                'task.assign',
                'ticket.manage',
                'panic.receive',
                'guestbook.view',
                'report.admin.download',
                'dashboard.analytics',
                'profile.view.own',
            ],
            'guru' => [
                'announcement.create.unit',
                'announcement.view',
                'agenda.create',
                'agenda.view.all',
                'group.request',
                'leave.request',
                'facility.book',
                'task.receive',
                'ticket.report',
                'profile.view.own',
            ],
            'tata_usaha' => [
                'announcement.create.campus',
                'announcement.create.unit',
                'announcement.view',
                'agenda.create',
                'agenda.view.all',
                'leave.request',
                'leave.monitor.all',
                'facility.book',
                'facility.screen.external',
                'facility.schedule.view',
                'task.receive',
                'ticket.report',
                'ticket.manage',
                'panic.receive',
                'guestbook.view',
                'shiftlog.read',
                'letter.manage',
                'report.admin.download',
                'profile.view.own',
            ],
            'satpam_pelaksana' => [
                'announcement.view',
                'leave.request',
                'facility.schedule.view',
                'task.receive',
                'ticket.report',
                'panic.trigger',
                'guestbook.record',
                'shiftlog.write',
                'profile.view.own',
            ],
            'umum' => [
                'facility.book.public',
            ],
        ];

        foreach ($roles as $role => $perms) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web'])->syncPermissions($perms);
        }
    }
}
