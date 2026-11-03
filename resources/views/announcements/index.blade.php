<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Papan Informasi & Siaran — SIAP PL Deltamas</title>
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
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo" class="h-10 w-10 object-contain">
                    <div>
                        <h1 class="text-sm sm:text-base font-bold text-white tracking-tight flex items-center gap-2">
                            SIAP PL Deltamas
                        </h1>
                        <p class="text-[11px] text-slate-400">Pusat Informasi & Pengumuman</p>
                    </div>
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition flex items-center gap-1.5 border border-slate-700">
                    <span>&larr; Dashboard</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-semibold transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full space-y-6">

        <!-- Header Action & Filters (COM-04 & COM-05) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight flex items-center gap-2">
                    <span>Papan Siaran Resmi</span>
                    <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-300 border border-blue-500/30">
                        {{ $announcements->count() }} Aktif
                    </span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    Pengumuman instruksional satu arah tingkat Kampus dan Unit sekolah.
                </p>
            </div>

            <div class="flex items-center space-x-3">
                @if($canCreateCampus || $canCreateUnit)
                    <a href="{{ route('announcements.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Siaran Baru</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2">
                <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Pill Tabs (COM-05) -->
        <div class="flex items-center space-x-2 text-xs">
            <a href="{{ route('announcements.index', ['filter' => 'all']) }}" 
               class="px-3.5 py-1.5 rounded-lg border font-semibold transition {{ $filter === 'all' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white' }}">
                Semua Informasi
            </a>
            <a href="{{ route('announcements.index', ['filter' => 'campus']) }}" 
               class="px-3.5 py-1.5 rounded-lg border font-semibold transition {{ $filter === 'campus' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white' }}">
                Siaran Kampus
            </a>
            <a href="{{ route('announcements.index', ['filter' => 'unit']) }}" 
               class="px-3.5 py-1.5 rounded-lg border font-semibold transition {{ $filter === 'unit' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white' }}">
                Siaran Unit Saya
            </a>
        </div>

        <!-- Announcements Feed List -->
        <div class="space-y-4">
            @forelse($announcements as $item)
                @php
                    $isRead = isset($readStatus[$item->id]) && $readStatus[$item->id] !== null;
                    $canTrackReceipts = $user->can('announcement.readreceipt.view') || $item->sender_id === $user->id;
                @endphp
                <div class="rounded-2xl border {{ $item->is_urgent ? 'bg-gradient-to-r from-rose-950/30 via-slate-900/90 to-slate-900/90 border-rose-500/40 shadow-rose-950/20' : 'bg-slate-800/50 border-slate-800' }} p-5 sm:p-6 space-y-4 shadow-xl backdrop-blur transition hover:border-slate-700">

                    <!-- Meta Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                        <div class="flex items-center space-x-2.5 flex-wrap gap-y-1">
                            @if($item->scope === 'campus')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                    Kampus PL Deltamas
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                    Unit {{ $item->unit_code }}
                                </span>
                            @endif

                            @if($item->category === 'reminder')
                                <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    Reminder
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-semibold bg-slate-700 text-slate-300">
                                    Pesan Baru
                                </span>
                            @endif

                            @if($item->is_urgent)
                                <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-extrabold bg-rose-500/30 text-rose-300 border border-rose-500/40 animate-pulse">
                                    PENTING
                                </span>
                            @endif
                        </div>

                        <div class="text-[11px] text-slate-400 font-mono flex items-center gap-2">
                            <span>{{ \Carbon\Carbon::parse($item->publish_at)->translatedFormat('d M Y, H:i') }} WIB</span>
                            @if($item->display_until)
                                <span>· Tayang s.d. {{ \Carbon\Carbon::parse($item->display_until)->translatedFormat('d M') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div class="text-sm sm:text-base text-slate-200 font-medium leading-relaxed">
                        {{ $item->body }}
                    </div>

                    <!-- Footer: Sender & Read Status -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 text-xs">
                        <div class="flex items-center space-x-2 text-slate-400">
                            <span class="h-6 w-6 rounded-full bg-slate-700 flex items-center justify-center font-bold text-[10px] text-white">
                                {{ substr($item->sender_name, 0, 1) }}
                            </span>
                            <span>Oleh: <strong class="text-slate-300">{{ $item->sender_name }}</strong> ({{ $item->sender_title }})</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <!-- Read/Unread Marker -->
                            @if($isRead)
                                <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-400 font-mono bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-lg">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Sudah Dibaca</span>
                                </span>
                            @else
                                <form action="{{ route('announcements.read', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 text-[11px] font-semibold transition flex items-center gap-1">
                                        <span>Tandai Terbaca</span>
                                        <span>&check;</span>
                                    </button>
                                </form>
                            @endif

                            <!-- Link to Read Receipts Report (COM-02) -->
                            @if($canTrackReceipts)
                                <a href="{{ route('announcements.receipts', $item->id) }}" 
                                   class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-[11px] font-semibold transition flex items-center gap-1.5"
                                   title="Pantau daftar pegawai yang sudah dan belum membaca">
                                    <svg class="h-3.5 w-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <span>Laporan Keterbacaan</span>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center py-12 border border-dashed border-slate-800 rounded-2xl bg-slate-850/40 p-8 space-y-3">
                    <div class="h-12 w-12 rounded-full bg-slate-800 flex items-center justify-center mx-auto text-slate-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Tidak ada siaran untuk kategori ini</h3>
                    <p class="text-xs text-slate-400">Seluruh pengumuman resmi terbaru akan ditampilkan secara real-time di halaman ini.</p>
                </div>
            @endforelse
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/60 py-4 text-center text-xs text-slate-500">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · SIAP PL Deltamas
    </footer>

</body>
</html>
