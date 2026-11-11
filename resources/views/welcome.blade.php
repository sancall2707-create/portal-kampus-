<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAP PL Deltamas — Sistem Informasi & Administrasi Pegawai</title>
    @include('partials.design-head')
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#102433">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIAP PL">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased selection:bg-blue-800 selection:text-white dark:bg-slate-950 dark:text-slate-100">
    @php
        $dummyUsers = [
            ['nip' => 'PL0001', 'role' => 'Koordinator', 'badge' => 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-500/30', 'name' => 'Br. Stefanus Ngadenin, FIC', 'title' => 'Koordinator Sekolah PL Deltamas', 'unit' => 'Seluruh Kampus', 'desc' => 'Super Admin: sapaan wajib, broadcast kampus, approval akhir izin & fasilitas, audit log.'],
            ['nip' => 'PL1001', 'role' => 'Kepala Sekolah', 'badge' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/30', 'name' => 'Maria Theresia Sulastri, S.Pd', 'title' => 'Kepala Sekolah PG-TK', 'unit' => 'PG-TK', 'desc' => 'Validasi izin staf PG-TK, approval fasilitas PG-TK, broadcast unit.'],
            ['nip' => 'PL1002', 'role' => 'Kepala Sekolah', 'badge' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/30', 'name' => 'Antonius Joko Waluyo, S.Pd', 'title' => 'Kepala Sekolah SD', 'unit' => 'SD', 'desc' => 'Validasi izin staf SD, delegasi Plt, penunjukan guru inval.'],
            ['nip' => 'PL1003', 'role' => 'Kepala Sekolah', 'badge' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/30', 'name' => 'Agustinus Tri Wahyudi, M.Pd', 'title' => 'Kepala Sekolah SMP', 'unit' => 'SMP', 'desc' => 'Validasi izin SMP, supervisi grup komunikasi unit SMP.'],
            ['nip' => 'PL1004', 'role' => 'Kepala Sekolah', 'badge' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-500/30', 'name' => 'Drs. FX. Hartono', 'title' => 'Kepala Sekolah SMA', 'unit' => 'SMA', 'desc' => 'Validasi izin SMA, tasking lapangan pelaksana SMA.'],
            ['nip' => 'PL2001', 'role' => 'Guru (Single)', 'badge' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30', 'name' => 'Bernadetha Rini Hastuti, S.Pd', 'title' => 'Guru Kelas SD', 'unit' => 'SD', 'desc' => 'Pengajuan izin/sakit mandiri, pinjam aula/kendaraan, tiket kerusakan.'],
            ['nip' => 'PL2002', 'role' => 'Guru (Multi-Unit)', 'badge' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border-amber-200 dark:border-amber-500/30', 'name' => 'Yohanes Batista Sandiwan, S.Kom', 'title' => 'Guru Informatika (SMP & SMA)', 'unit' => 'SMP & SMA', 'desc' => 'Simulasi guru multi-unit: mengajar di 2 unit sekolah sekaligus.'],
            ['nip' => 'PL2003', 'role' => 'Guru (Single)', 'badge' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30', 'name' => 'Elisabeth Dewi Anggraini, S.Pd', 'title' => 'Guru Kelas TK B', 'unit' => 'PG-TK', 'desc' => 'Alur pengajuan izin internal jenjang anak usia dini PG-TK.'],
            ['nip' => 'PL3001', 'role' => 'Tata Usaha (TU)', 'badge' => 'bg-cyan-100 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-500/30', 'name' => 'Theresia Ratna Juwita, A.Md', 'title' => 'Staf TU Kampus', 'unit' => 'SD / Kampus', 'desc' => 'Screening dokumen peminjam eksternal, register surat otomatis, rekap absensi.'],
            ['nip' => 'PL4001', 'role' => 'Satpam', 'badge' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-500/30', 'name' => 'Petrus Joko Santoso', 'title' => 'Danru Satpam Pos Gerbang', 'unit' => 'SD / Gerbang', 'desc' => 'Buku tamu gerbang, buku jaga/shift, verifikasi e-ticket QR, Panic Button.'],
            ['nip' => 'PL4002', 'role' => 'Pelaksana / Teknisi', 'badge' => 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-500/30', 'name' => 'Ignatius Budi Prasetyo', 'title' => 'Teknisi Sarpras & Lapangan', 'unit' => 'SMA / Sarpras', 'desc' => 'Penerimaan direct tasking pimpinan, penanganan tiket kerusakan, konfirmasi tugas.'],
            ['nip' => 'sekretariat.paroki@gmail.com', 'role' => 'Umum (Eksternal)', 'badge' => 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-600', 'name' => 'Ignatius Denny', 'title' => 'Sie Kepemudaan Paroki Cikarang', 'unit' => 'Eksternal', 'desc' => 'Pengujian peminjaman fasilitas publik (Gua Maria, Kapel, Aula SMA) via Google SSO.']
        ];
    @endphp

    <header class="border-b border-white/10 bg-ink-950 text-white">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-full bg-white p-1 ring-2 ring-gold-500/70">
                    <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo PL Deltamas" class="h-full w-full rounded-full object-contain" />
                </span>
                <span>
                    <span class="block font-display text-base font-semibold leading-tight">SIAP PL Deltamas</span>
                    <span class="block text-[11px] text-slate-600 dark:text-slate-400">Kampus Pangudi Luhur Deltamas</span>
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleTheme()" class="grid h-10 w-10 place-items-center rounded-full border border-white/15 text-slate-200" title="Ganti Tema">
                    <i class="bi bi-sun-fill hidden text-amber-400 dark:inline"></i>
                    <i class="bi bi-moon-stars-fill inline dark:hidden"></i>
                </button>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-3.5 py-2 text-xs font-semibold text-ink-950">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk</span>
                </a>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 space-y-8 px-4 py-8 sm:px-6">
        <section class="grid items-end gap-8 lg:grid-cols-[minmax(0,1.3fr)_minmax(16rem,0.7fr)]">
            <div class="space-y-4">
                <p class="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-gold-600 dark:text-gold-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Portal Pengujian SIAP PL Deltamas
                </p>
                <h1 class="font-display text-3xl leading-tight text-slate-900 dark:text-white sm:text-5xl">
                    Portal Internal Pegawai & Pengujian Sistem
                </h1>
                <p class="max-w-2xl text-sm leading-relaxed text-slate-600 dark:text-slate-300 sm:text-base">
                    Sistem koordinasi resmi satu arah, perizinan berjenjang, peminjaman aset kampus, penugasan operasional, dan buku tamu digital.
                </p>
                <div class="flex flex-wrap gap-2 text-[11px] font-mono">
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Laravel 12.x</span>
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">MariaDB 10.11</span>
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">39 Tabel Aktif</span>
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">41 Hak Akses RBAC</span>
                </div>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em]">Password Seluruh Akun</p>
                <p class="mt-2 font-mono text-2xl font-semibold">password123</p>
                <p class="mt-2 text-xs leading-relaxed">Pilih salah satu peran untuk simulasi hak akses dan alur kerja di portal.</p>
            </div>
        </section>

        <section class="grid gap-3 sm:grid-cols-3">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">Skema Basis Data</p>
                <p class="font-display mt-2 text-3xl text-slate-900 dark:text-white">39 Tabel</p>
                <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">402 kolom & 77 relasi FK terdaftar aktif di MariaDB.</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">Akses Kontrol (RBAC)</p>
                <p class="font-display mt-2 text-3xl text-slate-900 dark:text-white">6 Peran / 41 Izin</p>
                <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">Dilindungi penuh oleh Spatie Laravel Permission.</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">Unit Terpadu</p>
                <p class="font-display mt-2 text-3xl text-slate-900 dark:text-white">4 Unit Kampus</p>
                <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">PG-TK (satu unit), SD, SMP, dan SMA terhubung dalam satu portal.</p>
            </article>
        </section>

        <section class="space-y-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="font-display text-3xl text-slate-900 dark:text-white">Daftar Akun Pengujian Sistem</h2>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Pilih salah satu peran untuk simulasi hak akses dan alur kerja di portal.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:hidden">
                @foreach($dummyUsers as $u)
                    <article class="space-y-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-full border px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $u['badge'] }}">{{ $u['role'] }}</span>
                            <span class="rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 font-mono text-[11px] font-bold text-blue-700 dark:border-slate-700 dark:bg-slate-950 dark:text-blue-300">{{ $u['nip'] }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $u['name'] }}</p>
                            <p class="text-xs text-slate-600 dark:text-slate-300">{{ $u['title'] }}</p>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-2 text-xs dark:border-slate-800">
                            <span class="text-slate-600 dark:text-slate-300">Penempatan</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $u['unit'] }}</span>
                        </div>
                        <p class="rounded-xl bg-slate-50 p-2.5 text-[11px] leading-relaxed text-slate-600 dark:bg-slate-950 dark:text-slate-300">{{ $u['desc'] }}</p>
                        <a href="{{ route('login') }}" class="flex items-center justify-center gap-1.5 rounded-xl bg-ink-900 py-2 text-xs font-semibold text-white">
                            <span>Gunakan Akun Ini untuk Login</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 sm:block">
                <table class="w-full border-collapse text-left text-xs">
                    <thead class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-600 dark:text-slate-300 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold">No</th>
                            <th class="px-4 py-3 font-semibold">Peran (Role)</th>
                            <th class="px-4 py-3 font-semibold">NIP / Login</th>
                            <th class="px-4 py-3 font-semibold">Nama Pegawai & Jabatan</th>
                            <th class="px-4 py-3 font-semibold">Unit</th>
                            <th class="px-4 py-3 font-semibold">Cakupan Pengujian Wewenang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 dark:divide-slate-800 dark:text-slate-300">
                        @foreach($dummyUsers as $idx => $u)
                            <tr class="align-top hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $idx + 1 }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="rounded border px-2 py-0.5 text-[10px] font-bold uppercase {{ $u['badge'] }}">{{ $u['role'] }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 font-mono font-bold text-blue-700 dark:text-blue-300">{{ $u['nip'] }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $u['name'] }}</div>
                                    <div class="text-[11px] text-slate-600 dark:text-slate-300">{{ $u['title'] }}</div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ $u['unit'] }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $u['desc'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 py-4 text-center text-xs text-slate-600 dark:text-slate-300 dark:border-slate-800">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · SIAP PL Deltamas
    </footer>

    <script>
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
