<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kalender & Agenda — SIAP PL Deltamas</title>
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
                        <p class="text-[11px] text-slate-400">Kalender & Agenda Kegiatan</p>
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

        <!-- Header Action & Filters (AG-01 & AG-02) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight flex items-center gap-2">
                    <span>Kalender Agenda Kampus</span>
                    <span class="text-xs font-mono font-normal px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-300 border border-blue-500/30">
                        {{ $agendas->count() }} Kegiatan
                    </span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    Jadwal resmi terpadu acara kampus, unit sekolah, dan agenda pribadi.
                </p>
            </div>

            <div class="flex items-center space-x-3">
                @if($canCreate)
                    <a href="{{ route('agendas.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Jadwalkan Agenda</span>
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

        <!-- Filter Pills (AG-01) -->
        <div class="flex items-center space-x-2 text-xs overflow-x-auto pb-1">
            <a href="{{ route('agendas.index', ['category' => 'all']) }}" 
               class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $category === 'all' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white' }}">
                Semua Agenda
            </a>
            <a href="{{ route('agendas.index', ['category' => 'campus']) }}" 
               class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $category === 'campus' ? 'bg-purple-600 border-purple-500 text-white shadow' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white' }}">
                Tingkat Kampus
            </a>
            <a href="{{ route('agendas.index', ['category' => 'unit']) }}" 
               class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $category === 'unit' ? 'bg-blue-600 border-blue-500 text-white shadow' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white' }}">
                Tingkat Unit
            </a>
            <a href="{{ route('agendas.index', ['category' => 'personal']) }}" 
               class="px-3.5 py-1.5 rounded-lg border font-semibold transition shrink-0 {{ $category === 'personal' ? 'bg-emerald-600 border-emerald-500 text-white shadow' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white' }}">
                Agenda Pribadi
            </a>
        </div>

        <!-- Agenda List (AG-02) -->
        <div class="space-y-4">
            @forelse($agendas as $item)
                @php
                    $start = \Carbon\Carbon::parse($item->start_at);
                    $end = \Carbon\Carbon::parse($item->end_at);
                    $isSameDay = $start->isSameDay($end);
                @endphp
                <div class="rounded-2xl border {{ $item->blocks_booking ? 'border-amber-500/40 bg-gradient-to-r from-amber-950/20 via-slate-800/60 to-slate-800/60' : 'border-slate-800 bg-slate-800/50' }} p-5 sm:p-6 space-y-3 shadow-xl backdrop-blur transition hover:border-slate-700">

                    <!-- Meta Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                        <div class="flex items-center space-x-2.5 flex-wrap gap-y-1">
                            @if($item->category === 'campus')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                    Agenda Kampus
                                </span>
                            @elseif($item->category === 'unit')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                    Unit {{ $item->unit_code }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    Pribadi
                                </span>
                            @endif

                            @if($item->blocks_booking)
                                <span class="px-2 py-0.5 rounded-md text-[10px] uppercase font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-1" title="Agenda ini mengunci fasilitas kampus sehingga menolak reservasi lain yang bentrok">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <span>Memblokir Reservasi</span>
                                </span>
                            @endif
                        </div>

                        <!-- Date & Time Info -->
                        <div class="text-xs font-mono text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-lg shrink-0 flex items-center gap-2">
                            <svg class="h-3.5 w-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            @if($isSameDay)
                                <span>{{ $start->translatedFormat('d M Y') }} · {{ $start->format('H:i') }} - {{ $end->format('H:i') }} WIB</span>
                            @else
                                <span>{{ $start->translatedFormat('d M H:i') }} s.d. {{ $end->translatedFormat('d M H:i') }} WIB</span>
                            @endif
                        </div>
                    </div>

                    <!-- Agenda Title -->
                    <div class="space-y-1">
                        <h3 class="text-base sm:text-lg font-bold text-white tracking-tight">
                            {{ $item->title }}
                        </h3>
                        @if($item->description)
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                                {{ $item->description }}
                            </p>
                        @endif
                    </div>

                    <!-- Location & Creator -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-800/80 text-xs text-slate-400">
                        <div class="flex items-center space-x-2">
                            <svg class="h-4 w-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>
                                Lokasi: <strong class="text-white">{{ $item->facility_name ?? ($item->location_text ?? 'Kampus PL Deltamas') }}</strong>
                            </span>
                        </div>

                        <div class="text-[11px] text-slate-400">
                            Penyelenggara: <span class="text-slate-300 font-medium">{{ $item->creator_name }}</span> ({{ $item->creator_title }})
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center py-12 border border-dashed border-slate-800 rounded-2xl bg-slate-850/40 p-8 space-y-3">
                    <div class="h-12 w-12 rounded-full bg-slate-800 flex items-center justify-center mx-auto text-slate-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Tidak ada agenda untuk kategori ini</h3>
                    <p class="text-xs text-slate-400">Agenda kegiatan baru yang ditambahkan pimpinan atau panitia akan otomatis tampil di kalender ini.</p>
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
