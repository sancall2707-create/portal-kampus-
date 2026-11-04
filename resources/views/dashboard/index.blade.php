@extends('layouts.app')

@section('title', 'Beranda — SIAP PL Deltamas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8 space-y-6">

    <!-- Welcome Banner Card -->
    <div class="rounded-3xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-5 sm:p-7 relative overflow-hidden shadow-xl border border-blue-800/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/20 text-white text-[11px] font-semibold">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    Login Berhasil sebagai {{ strtoupper(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'STAF')) }}
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    Selamat datang, {{ $user->name }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-200">
                    {{ $user->job_title ?? 'Pegawai Kampus Pangudi Luhur Deltamas' }}
                </p>
            </div>
            <div class="text-left sm:text-right text-xs text-slate-300 font-mono bg-slate-950/40 p-3 rounded-2xl border border-white/10 shrink-0 space-y-0.5">
                <div>NIP: <span class="text-white font-bold">{{ $user->nip ?? '-' }}</span></div>
                <div>Tahun Ajaran: <span class="text-emerald-300 font-semibold">2026/2027 (Ganjil)</span></div>
                <div>Waktu: <span class="text-slate-200">{{ now()->format('d M Y, H:i') }} WIB</span></div>
            </div>
        </div>
    </div>

    <!-- Push Notification PWA Banner -->
    <div class="rounded-2xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/70 dark:bg-blue-950/30 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shrink-0 shadow-md">
                <i class="bi bi-bell-fill"></i>
            </div>
            <div>
                <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Notifikasi Smartphone (Web Push PWA)</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-200 dark:bg-blue-800 text-blue-900 dark:text-blue-100 uppercase">Baru</span>
                </h4>
                <p class="text-[11px] text-slate-600 dark:text-slate-300">
                    Dapatkan siaran kampus, persetujuan izin, dan alarm darurat langsung di status bar HP.
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <button type="button" onclick="openNotificationModal()" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                <i class="bi bi-bell text-xs"></i>
                <span>Atur Notifikasi</span>
            </button>
            <button type="button" onclick="handleSendTestPush()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5">
                <i class="bi bi-send-check text-xs"></i>
                <span>Tes Notifikasi</span>
            </button>
        </div>
    </div>

    <!-- Quick Navigation Hub -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs uppercase font-extrabold tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                <i class="bi bi-grid-fill text-blue-600 dark:text-blue-400"></i>
                <span>Akses Menu Cepat</span>
            </h3>
            <span class="text-[11px] text-slate-500 dark:text-slate-400">Atau buka tombol Menu di kanan atas</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-8 gap-3">
            <!-- 1. Papan Siaran -->
            <a href="{{ route('announcements.index') }}" class="p-4 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-center transition shadow-sm hover:shadow-md hover:border-blue-500 group flex flex-col items-center justify-center space-y-2">
                <div class="h-11 w-11 rounded-2xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="bi bi-megaphone-fill"></i>
                </div>
                <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Papan Siaran</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Broadcast Info</div>
            </a>

            <!-- 2. Kalender Agenda -->
            <a href="{{ route('agendas.index') }}" class="p-4 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-center transition shadow-sm hover:shadow-md hover:border-emerald-500 group flex flex-col items-center justify-center space-y-2">
                <div class="h-11 w-11 rounded-2xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Agenda Kampus</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Jadwal Kegiatan</div>
            </a>

            <!-- 3. Perizinan & Inval -->
            <a href="{{ route('leaves.index') }}" class="p-4 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-center transition shadow-sm hover:shadow-md hover:border-amber-500 group flex flex-col items-center justify-center space-y-2">
                <div class="h-11 w-11 rounded-2xl bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="bi bi-file-earmark-check-fill"></i>
                </div>
                <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Perizinan Staf</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Izin & Guru Inval</div>
            </a>

            <!-- 4. Fasilitas & E-Ticket -->
            <a href="{{ route('facilities.index') }}" class="p-4 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-center transition shadow-sm hover:shadow-md hover:border-indigo-500 group flex flex-col items-center justify-center space-y-2">
                <div class="h-11 w-11 rounded-2xl bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="bi bi-building"></i>
                </div>
                <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Fasilitas Kampus</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Reservasi & QR</div>
            </a>

            <!-- 5. Operasional Lapangan -->
            <a href="{{ route('operational.index') }}" class="p-4 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-center transition shadow-sm hover:shadow-md hover:border-cyan-500 group flex flex-col items-center justify-center space-y-2">
                <div class="h-11 w-11 rounded-2xl bg-cyan-100 dark:bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Operasional</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Tugas, Tiket, Tamu</div>
            </a>

            <!-- 6. Tata Persuratan -->
            <a href="{{ route('letters.index') }}" class="p-4 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-center transition shadow-sm hover:shadow-md hover:border-slate-500 group flex flex-col items-center justify-center space-y-2">
                <div class="h-11 w-11 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="bi bi-envelope-paper-fill"></i>
                </div>
                <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Tata Persuratan</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Arsip & No. Surat</div>
            </a>

            <!-- 7. Pesan Satu Arah Pribadi -->
            <a href="{{ route('messages.index') }}" class="p-4 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-center transition shadow-sm hover:shadow-md hover:border-purple-500 group flex flex-col items-center justify-center space-y-2 relative">
                <div class="h-11 w-11 rounded-2xl bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="bi bi-chat-left-lock-fill"></i>
                </div>
                <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Pesan</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Satu Arah</div>
            </a>

            <!-- 8. Kurikulum -->
            <button type="button" onclick="showDevelopmentNotice()" class="p-4 rounded-2xl bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-center transition shadow-sm hover:shadow-md hover:border-blue-500 group flex flex-col items-center justify-center space-y-2">
                <div class="h-11 w-11 rounded-2xl bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="bi bi-book-half"></i>
                </div>
                <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">Kurikulum</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Akademik & Nilai</div>
            </button>
        </div>
    </div>

    <!-- 3 Informational Cards: Mode Terang & Gelap Diselaraskan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Card 1: Profil Akun -->
        <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-6 space-y-4 shadow-sm transition-colors">
            <h3 class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold flex items-center gap-1.5">
                <i class="bi bi-person-circle text-blue-600 dark:text-blue-400 text-sm"></i>
                <span>Informasi Akun Pegawai</span>
            </h3>
            <div class="space-y-2.5 text-xs divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                <div class="pt-1 flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Peran Utama:</span>
                    <span class="font-bold text-purple-600 dark:text-purple-300">{{ ucfirst(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'Staf')) }}</span>
                </div>
                <div class="pt-2 flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Kategori:</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ ucfirst($user->staff_category ?? 'Umum') }}</span>
                </div>
                <div class="pt-2 flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Penempatan:</span>
                    <span class="font-medium text-emerald-600 dark:text-emerald-400 text-right">
                        {{ $user->units->isNotEmpty() ? $user->units->pluck('name')->implode(', ') : 'Kampus Deltamas' }}
                    </span>
                </div>
                <div class="pt-2 flex justify-between items-center">
                    <span class="text-slate-500 dark:text-slate-400">Status Akun:</span>
                    <span class="px-2.5 py-0.5 rounded text-[10px] bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-extrabold uppercase border border-emerald-200 dark:border-emerald-500/30">AKTIF</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Wewenang RBAC -->
        <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-6 space-y-4 shadow-sm transition-colors">
            <h3 class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold flex items-center gap-1.5">
                <i class="bi bi-shield-check text-purple-600 dark:text-purple-400 text-sm"></i>
                <span>Hak Akses Otorisasi (RBAC)</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Akun ini memiliki <strong class="text-slate-900 dark:text-white font-mono">{{ $user->getAllPermissions()->count() }} izin aktif</strong> yang diatur melalui paket <code class="text-blue-600 dark:text-blue-300 font-mono">spatie/laravel-permission</code>.
            </p>
            <div class="max-h-36 overflow-y-auto pr-1 space-y-1.5 text-[11px] font-mono text-slate-700 dark:text-slate-300">
                @foreach($user->getAllPermissions() as $p)
                    <div class="bg-slate-50 dark:bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <span class="text-slate-700 dark:text-slate-300">{{ $p->name }}</span>
                        <i class="bi bi-check-circle-fill text-emerald-500 text-xs"></i>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Card 3: Status Modul -->
        <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 p-6 space-y-4 shadow-sm transition-colors">
            <h3 class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold flex items-center gap-1.5">
                <i class="bi bi-layers-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                <span>Status Modul Sistem</span>
            </h3>
            <div class="space-y-2 text-xs text-slate-700 dark:text-slate-300">
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                    <span class="font-medium text-slate-800 dark:text-slate-200">Komunikasi & Fasilitas</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold flex items-center gap-1">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Aktif</span>
                    </span>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                    <span class="font-medium text-slate-800 dark:text-slate-200">Perizinan & Operasional</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold flex items-center gap-1">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Aktif</span>
                    </span>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                    <span class="font-medium text-slate-800 dark:text-slate-200">Modul Kurikulum</span>
                    <span class="text-amber-500 font-mono text-[10px] font-semibold flex items-center gap-1">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Pengembangan</span>
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
