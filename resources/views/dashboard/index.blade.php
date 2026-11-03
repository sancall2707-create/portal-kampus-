@extends('layouts.app')

@section('title', 'Beranda — SIAP PL Deltamas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Welcome Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white p-6 sm:p-7 relative overflow-hidden shadow-xl border border-blue-800">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-white/10 border border-white/20 text-white text-[11px] font-semibold">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    Login Berhasil sebagai {{ strtoupper(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'STAF')) }}
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                    Selamat datang, {{ $user->name }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-200">
                    {{ $user->job_title ?? 'Pegawai Kampus Pangudi Luhur Deltamas' }}
                </p>
            </div>
            <div class="text-left sm:text-right text-xs text-slate-300 font-mono bg-slate-950/40 p-3 rounded-xl border border-white/10 shrink-0">
                <div>NIP: <span class="text-white font-bold">{{ $user->nip ?? '-' }}</span></div>
                <div>Tahun Ajaran: <span class="text-emerald-300">2026/2027 (Ganjil)</span></div>
                <div>Login: <span class="text-slate-200">{{ now()->format('d M Y, H:i') }} WIB</span></div>
            </div>
        </div>
    </div>

    <!-- Quick Access Buttons -->
    <div class="flex flex-wrap gap-2.5">
        <a href="{{ route('announcements.index') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-white transition flex items-center gap-2 shadow-sm">
            <i class="bi bi-megaphone-fill text-blue-600 dark:text-blue-400"></i>
            <span>Papan Siaran (Broadcast)</span>
        </a>
        <a href="{{ route('agendas.index') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-white transition flex items-center gap-2 shadow-sm">
            <i class="bi bi-calendar3 text-emerald-600 dark:text-emerald-400"></i>
            <span>Kalender Agenda Kampus</span>
        </a>
        <a href="{{ route('greeting.show') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition flex items-center gap-2 shadow-sm">
            <i class="bi bi-chat-quote-fill text-amber-500"></i>
            <span>Buka Ulang Sapaan Koordinator</span>
        </a>
    </div>

    <!-- Role Badges & Permissions Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Card 1: Profil Akun -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 p-5 space-y-3 shadow-sm transition-colors">
            <h3 class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1.5">
                <i class="bi bi-person-circle text-blue-600 dark:text-blue-400 text-sm"></i>
                <span>Informasi Akun</span>
            </h3>
            <div class="space-y-2 text-xs divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
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
                    <span class="font-medium text-emerald-600 dark:text-emerald-400">
                        {{ $user->units->isNotEmpty() ? $user->units->pluck('name')->implode(', ') : 'Kampus Deltamas' }}
                    </span>
                </div>
                <div class="pt-2 flex justify-between">
                    <span class="text-slate-500 dark:text-slate-400">Status Akun:</span>
                    <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-semibold uppercase">Aktif</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Wewenang RBAC -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 p-5 space-y-3 shadow-sm transition-colors">
            <h3 class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1.5">
                <i class="bi bi-shield-check text-purple-600 dark:text-purple-400 text-sm"></i>
                <span>Hak Akses RBAC</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Akun ini memiliki <strong class="text-slate-900 dark:text-white font-mono">{{ $user->getAllPermissions()->count() }} izin aktif</strong> yang diatur melalui paket <code class="text-blue-600 dark:text-blue-300 font-mono">spatie/laravel-permission</code>.
            </p>
            <div class="max-h-36 overflow-y-auto pr-1 space-y-1 text-[11px] font-mono text-slate-700 dark:text-slate-300">
                @foreach($user->getAllPermissions() as $p)
                    <div class="bg-slate-50 dark:bg-slate-900/80 px-2 py-1 rounded border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-slate-700 dark:text-slate-300">{{ $p->name }}</span>
                        <i class="bi bi-check-circle-fill text-emerald-500 text-[10px]"></i>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Card 3: Modul Siap Digunakan -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 p-5 space-y-3 shadow-sm transition-colors">
            <h3 class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1.5">
                <i class="bi bi-grid-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                <span>Status Modul Sistem</span>
            </h3>
            <div class="space-y-1.5 text-xs text-slate-700 dark:text-slate-300">
                <div class="flex items-center justify-between p-1.5 rounded bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800">
                    <span>Layar Sapaan Koordinator</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold flex items-center gap-1">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Aktif</span>
                    </span>
                </div>
                <div class="flex items-center justify-between p-1.5 rounded bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800">
                    <span>Broadcast & Read Receipts</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold flex items-center gap-1">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Aktif</span>
                    </span>
                </div>
                <div class="flex items-center justify-between p-1.5 rounded bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800">
                    <span>Kalender & Agenda Kampus</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-mono text-[10px] font-bold flex items-center gap-1">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Aktif</span>
                    </span>
                </div>
                <div class="flex items-center justify-between p-1.5 rounded bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800">
                    <span>Perizinan & Guru Inval</span>
                    <span class="text-amber-500 font-mono text-[10px]">Next</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
