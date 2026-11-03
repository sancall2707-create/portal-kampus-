<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda — SIAP PL Deltamas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top App Bar -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo" class="h-10 w-10 object-contain">
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-white tracking-tight flex items-center gap-2">
                        SIAP PL Deltamas
                    </h1>
                    <p class="text-[11px] text-slate-400">
                        @if($user->units->isNotEmpty())
                            Unit {{ $user->units->pluck('code')->implode(' & ') }}
                        @else
                            Tingkat Kampus
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('announcements.index') }}" class="px-3 py-1.5 rounded-lg bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/40 text-blue-300 text-xs font-semibold transition flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Papan Siaran</span>
                </a>

                <div class="hidden sm:flex flex-col text-right">
                    <span class="text-xs font-semibold text-white">{{ $user->name }}</span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ $user->nip ?? $user->email }} · {{ ucfirst(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'Staf')) }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-semibold transition flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-6">

        <!-- Welcome Banner -->
        <div class="rounded-2xl bg-gradient-to-r from-blue-950/70 via-slate-800/60 to-indigo-950/70 border border-slate-700/60 p-6 sm:p-7 relative overflow-hidden shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/30 text-blue-300 text-[11px] font-semibold">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Login Berhasil sebagai {{ strtoupper(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'STAF')) }}
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                        Selamat datang, {{ $user->name }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300">
                        {{ $user->job_title ?? 'Pegawai Kampus Pangudi Luhur Deltamas' }}
                    </p>
                </div>
                <div class="text-left sm:text-right text-xs text-slate-400 font-mono bg-slate-900/60 p-3 rounded-xl border border-slate-800 shrink-0">
                    <div>NIP: <span class="text-white font-bold">{{ $user->nip ?? '-' }}</span></div>
                    <div>Tahun Ajaran: <span class="text-emerald-400">2026/2027 (Ganjil)</span></div>
                    <div>Login: <span class="text-slate-300">{{ now()->format('d M Y, H:i') }} WIB</span></div>
                </div>
            </div>
        </div>

        <!-- Quick Access Pill Buttons (PR-01) -->
        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('announcements.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-semibold text-white transition flex items-center gap-2 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                <span>Papan Siaran (Broadcast)</span>
            </a>
            <a href="{{ route('greeting.show') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-xs font-semibold text-slate-300 hover:text-white transition flex items-center gap-2 shadow-sm">
                <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                <span>Buka Ulang Sapaan Koordinator</span>
            </a>
        </div>

        <!-- Role Badges & Permissions Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

            <!-- Card 1: Profil Akun -->
            <div class="rounded-xl border border-slate-800 bg-slate-800/40 p-5 space-y-3">
                <h3 class="text-xs uppercase tracking-wider text-slate-400 font-bold flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Informasi Akun</span>
                </h3>
                <div class="space-y-2 text-xs divide-y divide-slate-800 text-slate-300">
                    <div class="pt-1 flex justify-between">
                        <span class="text-slate-400">Peran Utama:</span>
                        <span class="font-bold text-purple-300">{{ ucfirst(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'Staf')) }}</span>
                    </div>
                    <div class="pt-2 flex justify-between">
                        <span class="text-slate-400">Kategori:</span>
                        <span class="font-medium text-white">{{ ucfirst($user->staff_category ?? 'Umum') }}</span>
                    </div>
                    <div class="pt-2 flex justify-between">
                        <span class="text-slate-400">Penempatan:</span>
                        <span class="font-medium text-emerald-400">
                            {{ $user->units->isNotEmpty() ? $user->units->pluck('name')->implode(', ') : 'Kampus Deltamas' }}
                        </span>
                    </div>
                    <div class="pt-2 flex justify-between">
                        <span class="text-slate-400">Status Akun:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-300 font-semibold uppercase">Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Wewenang RBAC -->
            <div class="rounded-xl border border-slate-800 bg-slate-800/40 p-5 space-y-3">
                <h3 class="text-xs uppercase tracking-wider text-slate-400 font-bold flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Hak Akses RBAC</span>
                </h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Akun ini memiliki <strong class="text-white font-mono">{{ $user->getAllPermissions()->count() }} izin aktif</strong> yang diatur melalui paket <code class="text-blue-300 font-mono">spatie/laravel-permission</code>.
                </p>
                <div class="max-h-32 overflow-y-auto pr-1 space-y-1 text-[11px] font-mono text-slate-300">
                    @foreach($user->getAllPermissions() as $p)
                        <div class="bg-slate-900/80 px-2 py-1 rounded border border-slate-800 flex items-center justify-between">
                            <span class="text-slate-300">{{ $p->name }}</span>
                            <span class="text-emerald-400 text-[10px]">✓</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Card 3: Modul Siap Digunakan -->
            <div class="rounded-xl border border-slate-800 bg-slate-800/40 p-5 space-y-3">
                <h3 class="text-xs uppercase tracking-wider text-slate-400 font-bold flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z" />
                    </svg>
                    <span>Status Modul Sistem</span>
                </h3>
                <div class="space-y-1.5 text-xs text-slate-300">
                    <div class="flex items-center justify-between p-1.5 rounded bg-slate-900/60">
                        <span>Layar Sapaan Koordinator</span>
                        <span class="text-emerald-400 font-mono text-[10px] font-bold">Aktif ✓</span>
                    </div>
                    <div class="flex items-center justify-between p-1.5 rounded bg-slate-900/60">
                        <span>Broadcast & Read Receipts</span>
                        <span class="text-emerald-400 font-mono text-[10px] font-bold">Aktif ✓</span>
                    </div>
                    <div class="flex items-center justify-between p-1.5 rounded bg-slate-900/60">
                        <span>Kalender & Agenda Kampus</span>
                        <span class="text-amber-400 font-mono text-[10px]">Next</span>
                    </div>
                    <div class="flex items-center justify-between p-1.5 rounded bg-slate-900/60">
                        <span>Perizinan & Guru Inval</span>
                        <span class="text-amber-400 font-mono text-[10px]">Next</span>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/60 py-3 text-center text-xs text-slate-500">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · SIAP PL Deltamas
    </footer>

</body>
</html>
