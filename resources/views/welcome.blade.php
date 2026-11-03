<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAP PL Deltamas — Sistem Informasi & Administrasi Pegawai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
    <script>
        if (localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen antialiased flex flex-col justify-between transition-colors duration-200 selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="border-b border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/80 backdrop-blur sticky top-0 z-50 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center font-bold text-white shadow-lg shadow-blue-500/20">
                    PL
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                        SIAP PL Deltamas
                        <span class="text-[10px] font-semibold tracking-wider uppercase bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30 px-2 py-0.5 rounded-full">Dev Preview</span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kampus Pangudi Luhur Deltamas (PG-TK, SD, SMP, SMA)</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Ganti Tema">
                    <i id="themeIconSun" class="bi bi-sun-fill text-amber-500 hidden dark:inline"></i>
                    <i id="themeIconMoon" class="bi bi-moon-stars-fill text-slate-600 dark:hidden inline"></i>
                </button>

                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-1.5">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk Portal</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-8">

        <!-- Hero Section -->
        <div class="rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 sm:p-8 relative overflow-hidden shadow-xl border border-blue-800">
            <div class="relative z-10 max-w-3xl space-y-2">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-snug">
                    Portal Internal Terpadu Guru & Karyawan
                </h2>
                <p class="text-sm sm:text-base text-slate-200 leading-relaxed">
                    Sistem koordinasi satu arah, perizinan berjenjang, peminjaman aset kampus, penugasan operasional, dan buku tamu gerbang. Dilengkapi dukungan tema terang & gelap, ikon Bootstrap Icons standar, dan 39 tabel aktif.
                </p>
                <div class="pt-2 flex flex-wrap gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded-md bg-white/10 border border-white/20 text-white font-mono">Laravel 12.x</span>
                    <span class="px-2.5 py-1 rounded-md bg-white/10 border border-white/20 text-white font-mono">MariaDB 10.11</span>
                    <span class="px-2.5 py-1 rounded-md bg-white/10 border border-white/20 text-white font-mono">39 Tabel Aplikasi</span>
                    <span class="px-2.5 py-1 rounded-md bg-white/10 border border-white/20 text-white font-mono">41 RBAC Permissions</span>
                    <span class="px-2.5 py-1 rounded-md bg-blue-500/30 border border-blue-400 text-white font-semibold">Non E-Raport (Tahap Awal)</span>
                </div>
            </div>
        </div>

        <!-- Testing Credentials Section -->
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        Akun Dummy Pengujian (Tahap Pengembangan)
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan akun-akun di bawah ini untuk pengujian peran, validasi wewenang, dan simulasi alur sistem.</p>
                </div>
                <div class="bg-amber-100 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30 text-amber-800 dark:text-amber-300 px-3 py-1.5 rounded-lg text-xs font-mono shrink-0">
                    Password Seluruh Akun: <span class="font-bold underline text-slate-900 dark:text-white">password123</span>
                </div>
            </div>

            <!-- Table of Dummy Users -->
            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 shadow-sm dark:shadow-xl backdrop-blur transition-colors">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                        <tr>
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Peran (Role)</th>
                            <th class="py-3 px-4">NIP / Akun Login</th>
                            <th class="py-3 px-4">Nama Pegawai / Jabatan</th>
                            <th class="py-3 px-4">Unit Sekolah</th>
                            <th class="py-3 px-4">Cakupan Pengujian Wewenang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">1</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30 font-semibold text-[10px]">Koordinator</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL0001</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Br. Stefanus Ngadenin, FIC</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Koordinator Sekolah PL Deltamas</div></td>
                            <td class="py-3 px-4"><span class="text-purple-600 dark:text-purple-400 font-semibold">Seluruh Kampus</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Super Admin: kelola user, sapaan wajib, broadcast kampus, approval akhir izin & fasilitas.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">2</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30 font-semibold text-[10px]">Kepala Sekolah</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL1001</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Maria Theresia Sulastri, S.Pd</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Kepala Sekolah PG-TK</div></td>
                            <td class="py-3 px-4"><span class="text-blue-600 dark:text-blue-400 font-semibold">PG-TK</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Validasi izin staf PG-TK, approval fasilitas PG-TK, broadcast unit.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">3</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30 font-semibold text-[10px]">Kepala Sekolah</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL1002</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Antonius Joko Waluyo, S.Pd</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Kepala Sekolah SD</div></td>
                            <td class="py-3 px-4"><span class="text-blue-600 dark:text-blue-400 font-semibold">SD</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Validasi izin staf SD, delegasi Plt ke guru senior, penunjukan guru inval.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">4</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30 font-semibold text-[10px]">Kepala Sekolah</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL1003</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Agustinus Tri Wahyudi, M.Pd</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Kepala Sekolah SMP</div></td>
                            <td class="py-3 px-4"><span class="text-blue-600 dark:text-blue-400 font-semibold">SMP</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Validasi izin SMP, supervisi grup komunikasi unit SMP.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">5</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30 font-semibold text-[10px]">Kepala Sekolah</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL1004</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Drs. FX. Hartono</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Kepala Sekolah SMA</div></td>
                            <td class="py-3 px-4"><span class="text-blue-600 dark:text-blue-400 font-semibold">SMA</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Validasi izin SMA, tasking lapangan pelaksana SMA.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">6</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 font-semibold text-[10px]">Guru (Single)</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL2001</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Bernadetha Rini Hastuti, S.Pd</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Guru Kelas SD</div></td>
                            <td class="py-3 px-4"><span class="text-emerald-600 dark:text-emerald-400 font-semibold">SD</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Pengajuan izin/sakit mandiri, pinjam aula/kendaraan, tiket kerusakan sarpras.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition bg-indigo-50/50 dark:bg-indigo-950/20">
                            <td class="py-3 px-4 font-mono text-slate-400">7</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30 font-semibold text-[10px]">Guru (Multi-Unit)</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL2002</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Yohanes Batista Sandiwan, S.Kom</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Guru Informatika (SMP & SMA)</div></td>
                            <td class="py-3 px-4"><span class="text-amber-600 dark:text-amber-400 font-semibold">SMP & SMA</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Pengujian multi-unit: guru mengajar di 2 unit sekolah sekaligus.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">8</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 font-semibold text-[10px]">Guru (Single)</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL2003</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Elisabeth Dewi Anggraini, S.Pd</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Guru Kelas TK B</div></td>
                            <td class="py-3 px-4"><span class="text-emerald-600 dark:text-emerald-400 font-semibold">PG-TK</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Alur pengajuan izin internal jenjang anak usia dini PG-TK.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">9</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-cyan-100 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/30 font-semibold text-[10px]">Tata Usaha (TU)</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL3001</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Theresia Ratna Juwita, A.Md</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Staf TU Kampus</div></td>
                            <td class="py-3 px-4"><span class="text-cyan-600 dark:text-cyan-400 font-semibold">SD / Kampus</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Screening dokumen peminjam eksternal, penomoran surat otomatis, rekap absensi.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">10</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 font-semibold text-[10px]">Satpam</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL4001</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Petrus Joko Santoso</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Danru Satpam Pos Gerbang</div></td>
                            <td class="py-3 px-4"><span class="text-rose-600 dark:text-rose-400 font-semibold">SD / Gerbang</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Buku tamu gerbang, buku jaga/shift, verifikasi e-ticket QR, Panic Button.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">11</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 font-semibold text-[10px]">Pelaksana / Teknisi</span></td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-white">PL4002</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Ignatius Budi Prasetyo</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Teknisi Sarpras & Lapangan</div></td>
                            <td class="py-3 px-4"><span class="text-rose-600 dark:text-rose-400 font-semibold">SMA / Sarpras</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Penerimaan direct tasking pimpinan, penanganan tiket kerusakan, konfirmasi tugas selesai.</td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">12</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-500/20 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-500/30 font-semibold text-[10px]">Umum (Eksternal)</span></td>
                            <td class="py-3 px-4 font-mono text-slate-700 dark:text-slate-300 text-[11px]">sekretariat.paroki@gmail.com</td>
                            <td class="py-3 px-4"><div class="font-medium text-slate-900 dark:text-white">Ignatius Denny</div><div class="text-[11px] text-slate-500 dark:text-slate-400">Sie Kepemudaan Paroki Cikarang</div></td>
                            <td class="py-3 px-4"><span class="text-slate-500 dark:text-slate-400">Eksternal</span></td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">Pengujian peminjaman fasilitas publik (Gua Maria, Kapel, Aula SMA) via Google SSO.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Architecture Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/50 p-5 space-y-2 shadow-sm transition-colors">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Database Schema</div>
                <div class="text-2xl font-bold text-slate-900 dark:text-white">39 Tabel</div>
                <p class="text-xs text-slate-500 dark:text-slate-400">402 kolom, 77 relasi FK terdaftar aktif di MariaDB tanpa tabel E-Raport sesuai instruksi.</p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/50 p-5 space-y-2 shadow-sm transition-colors">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Akses Kontrol (RBAC)</div>
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">6 Peran / 41 Izin</div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Spatie Laravel Permission aktif melindungi otorisasi tiap peran di tingkat kampus & unit.</p>
            </div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/50 p-5 space-y-2 shadow-sm transition-colors">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">Unit Sekolah</div>
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">4 Unit Terpadu</div>
                <p class="text-xs text-slate-500 dark:text-slate-400">PG-TK (satu unit), SD, SMP, dan SMA terhubung dalam satu hierarki kampus Deltamas.</p>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white/60 dark:bg-slate-900/60 py-4 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors">
        &copy; 2026 Yayasan Pangudi Luhur — Kampus Deltamas. Hak cipta dilindungi undang-undang.
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
    </script>
</body>
</html>
