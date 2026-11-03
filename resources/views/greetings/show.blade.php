<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sapaan Koordinator — SIAP PL Deltamas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 text-slate-100 min-h-screen flex flex-col justify-between antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top Minimal Bar -->
    <header class="w-full border-b border-slate-800/80 bg-slate-950/40 backdrop-blur">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo" class="h-8 w-8 object-contain">
                <span class="text-xs font-bold text-white tracking-wide">SIAP PL Deltamas</span>
                <span class="text-[10px] uppercase font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-full">Sapaan Intersepsi</span>
            </div>
            <div class="text-xs text-slate-400 font-mono">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </header>

    <!-- Main Greeting Modal/Card -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-6">
        <div class="w-full max-w-2xl bg-slate-900/90 backdrop-blur-2xl border border-slate-700/80 rounded-3xl p-6 sm:p-9 shadow-2xl shadow-black/80 relative overflow-hidden space-y-6">

            <!-- Decorative background glow -->
            <div class="absolute -right-24 -top-24 w-56 h-56 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Author Header -->
            <div class="flex items-center space-x-4 border-b border-slate-800 pb-5">
                <div class="relative">
                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center font-bold text-xl text-white shadow-lg border border-blue-400/30">
                        {{ substr($author->name ?? 'Br', 0, 2) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-emerald-500 border-2 border-slate-900" title="Online"></span>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-blue-400">Pesan dari Pimpinan Kampus</div>
                    <h1 class="text-lg sm:text-xl font-bold text-white">{{ $author->name ?? 'Koordinator Sekolah' }}</h1>
                    <p class="text-xs text-slate-400">{{ $author->job_title ?? 'Koordinator Kampus Pangudi Luhur Deltamas' }}</p>
                </div>
            </div>

            <!-- Main Greeting Message -->
            <div class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight leading-snug">
                    {{ $greeting->title }}
                </h2>
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 text-slate-200 text-sm leading-relaxed">
                    {{ $greeting->message }}
                </div>
            </div>

            <!-- Highlights Box (COM-01) -->
            @if($highlights->isNotEmpty())
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold tracking-wider text-slate-400 flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Highlight Informasi Krusial Hari Ini</span>
                        </span>
                        <span class="text-[10px] text-slate-500 font-mono">{{ $highlights->count() }} Poin Penting</span>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($highlights as $h)
                            <div class="p-3.5 rounded-xl border {{ $h->is_urgent ? 'bg-rose-500/10 border-rose-500/30' : 'bg-slate-800/40 border-slate-800' }} flex items-start space-x-3 transition">
                                <span class="h-2 w-2 rounded-full {{ $h->is_urgent ? 'bg-rose-400 animate-ping' : 'bg-blue-400' }} mt-1.5 shrink-0"></span>
                                <div class="space-y-0.5 flex-1">
                                    <div class="text-xs font-bold {{ $h->is_urgent ? 'text-rose-300' : 'text-white' }} flex items-center gap-2">
                                        <span>{{ $h->title }}</span>
                                        @if($h->is_urgent)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] uppercase bg-rose-500/20 text-rose-300 font-bold border border-rose-500/30">Penting</span>
                                        @endif
                                    </div>
                                    @if($h->detail)
                                        <p class="text-xs text-slate-300 leading-relaxed">{{ $h->detail }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Bottom CTA Button (COM-01 & COM-02) -->
            <form action="{{ route('greeting.acknowledge') }}" method="POST" class="pt-2">
                @csrf
                <input type="hidden" name="greeting_id" value="{{ $greeting->id }}">
                <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-500 active:scale-[0.99] font-bold text-sm sm:text-base text-white shadow-xl shadow-blue-600/30 transition duration-150 flex items-center justify-center space-x-2.5 group">
                    <span>{{ $greeting->cta_label ?? 'Masuk Beranda Kampus' }}</span>
                    <svg class="h-5 w-5 transform group-hover:translate-x-1 transition duration-150" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
                <p class="text-center text-[11px] text-slate-500 mt-2.5">
                    Menekan tombol ini mencatat konfirmasi keterbacaan (read receipt) resmi ke sistem.
                </p>
            </form>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-800/80 bg-slate-950/40 py-3 text-center text-xs text-slate-500">
        &copy; 2026 Yayasan Pangudi Luhur Kota Deltamas · SIAP PL Deltamas
    </footer>

</body>
</html>
