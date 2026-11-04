<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIAP PL Deltamas — Sistem Informasi & Administrasi Pegawai</title>

    <!-- Tailwind CSS CDN -->
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
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIAP PL">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

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
    <header class="border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/90 backdrop-blur sticky top-0 z-40 transition-colors shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="h-10 w-10 rounded-full bg-white/90 dark:bg-white/10 p-0.5 shadow-sm border border-slate-200 dark:border-white/15 flex items-center justify-center shrink-0 overflow-hidden">
                    <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo" class="h-full w-full object-contain rounded-full drop-shadow-xs">
                </div>
                <div>
                    <h1 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                        SIAP PL Deltamas
                    </h1>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate max-w-[170px] sm:max-w-xs">
                        Kampus Pangudi Luhur Deltamas
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Ganti Tema">
                    <i id="themeIconSun" class="bi bi-sun-fill text-amber-500 hidden dark:inline"></i>
                    <i id="themeIconMoon" class="bi bi-moon-stars-fill text-slate-600 dark:hidden inline"></i>
                </button>

                <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-1.5">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex-1 w-full space-y-6">

        <!-- Hero Card (Clean & Responsive) -->
        <div class="rounded-3xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-5 sm:p-7 relative overflow-hidden shadow-xl border border-blue-800/80">
            <div class="space-y-2.5 relative z-10">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/20 text-white text-[11px] font-semibold">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    <span>Portal Pengujian SIAP PL Deltamas</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight leading-snug">
                    Portal Internal Pegawai & Pengujian Sistem
                </h2>
                <p class="text-xs sm:text-sm text-slate-200 leading-relaxed max-w-2xl">
                    Sistem koordinasi resmi satu arah, perizinan berjenjang, peminjaman aset kampus, penugasan operasional, dan buku tamu digital.
                </p>
                <div class="pt-2 flex flex-wrap gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded-lg bg-white/10 border border-white/15 text-white font-mono text-[11px]">Laravel 12.x</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10 border border-white/15 text-white font-mono text-[11px]">MariaDB 10.11</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10 border border-white/15 text-white font-mono text-[11px]">39 Tabel Aktif</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white/10 border border-white/15 text-white font-mono text-[11px]">41 Hak Akses RBAC</span>
                </div>
            </div>
        </div>

        <!-- Testing Credentials Section -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-people-fill text-blue-600 dark:text-blue-400"></i>
                        <span>Daftar Akun Pengujian Sistem</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pilih salah satu peran untuk simulasi hak akses dan alur kerja di portal.</p>
                </div>
                <div class="inline-flex items-center gap-2 bg-amber-50 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30 text-amber-800 dark:text-amber-300 px-3 py-1.5 rounded-xl text-xs font-mono shrink-0">
                    <i class="bi bi-key-fill text-amber-500"></i>
                    <span>Password Seluruh Akun: <strong class="font-bold underline text-slate-900 dark:text-white">password123</strong></span>
                </div>
            </div>

            <!-- Responsive Cards for Mobile View (Hidden on sm screens and up) -->
            <div class="grid grid-cols-1 gap-3 sm:hidden">
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

                @foreach($dummyUsers as $u)
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-4 space-y-2.5 shadow-sm transition-colors">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold border {{ $u['badge'] }}">
                                {{ $u['role'] }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[11px] font-mono font-bold text-blue-600 dark:text-blue-400">
                                {{ $u['nip'] }}
                            </span>
                        </div>

                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-white leading-snug">{{ $u['name'] }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $u['title'] }}</div>
                        </div>

                        <div class="pt-1.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Penempatan:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $u['unit'] }}</span>
                        </div>

                        <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800">
                            {{ $u['desc'] }}
                        </p>

                        <div class="pt-1">
                            <a href="{{ route('login') }}" class="w-full py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-xs">
                                <span>Gunakan Akun Ini untuk Login</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Table View for Desktop / Tablet View (Hidden on mobile) -->
            <div class="hidden sm:block overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 shadow-sm transition-colors">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700 font-semibold">
                        <tr>
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Peran (Role)</th>
                            <th class="py-3 px-4">NIP / Login</th>
                            <th class="py-3 px-4">Nama Pegawai & Jabatan</th>
                            <th class="py-3 px-4">Unit</th>
                            <th class="py-3 px-4">Cakupan Pengujian Wewenang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                        @foreach($dummyUsers as $idx => $u)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/60 transition">
                                <td class="py-3 px-4 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold border {{ $u['badge'] }}">
                                        {{ $u['role'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                    {{ $u['nip'] }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $u['name'] }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $u['title'] }}</div>
                                </td>
                                <td class="py-3 px-4 font-semibold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                    {{ $u['unit'] }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 text-xs">
                                    {{ $u['desc'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3 Architecture Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-5 space-y-1.5 shadow-sm transition-colors">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">Skema Basis Data</div>
                <div class="text-2xl font-extrabold text-slate-900 dark:text-white">39 Tabel</div>
                <p class="text-xs text-slate-500 dark:text-slate-400">402 kolom & 77 relasi FK terdaftar aktif di MariaDB.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-5 space-y-1.5 shadow-sm transition-colors">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">Akses Kontrol (RBAC)</div>
                <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">6 Peran / 41 Izin</div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Dilindungi penuh oleh Spatie Laravel Permission.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-5 space-y-1.5 shadow-sm transition-colors">
                <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">Unit Terpadu</div>
                <div class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">4 Unit Kampus</div>
                <p class="text-xs text-slate-500 dark:text-slate-400">PG-TK (satu unit), SD, SMP, dan SMA terhubung dalam satu portal.</p>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 bg-white/60 dark:bg-slate-900/60 py-4 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors">
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
