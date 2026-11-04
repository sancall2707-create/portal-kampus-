<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sapaan Koordinator — SIAP PL Deltamas</title>
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
    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIAP PL">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col justify-between antialiased transition-colors duration-200 selection:bg-blue-600 selection:text-white">

    <!-- Top Minimal Bar -->
    <header class="w-full border-b border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-950/40 backdrop-blur transition-colors">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="h-9 w-9 rounded-full bg-white p-1 flex items-center justify-center shrink-0 shadow-sm border border-slate-200/80 dark:border-white/20">
                    <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo PL Deltamas" class="h-full w-full object-contain rounded-full" />
                </div>
                <span class="text-xs font-bold text-slate-900 dark:text-white tracking-wide">SIAP PL Deltamas</span>
                <span class="text-[10px] uppercase font-semibold bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-full">Sapaan Intersepsi</span>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="toggleTheme()" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-700 transition" title="Ganti Tema">
                    <i id="themeIconSun" class="bi bi-sun-fill text-amber-500 hidden dark:inline"></i>
                    <i id="themeIconMoon" class="bi bi-moon-stars-fill text-slate-600 dark:hidden inline"></i>
                </button>
                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono hidden sm:block">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </div>
    </header>

    <!-- Main Greeting Modal/Card -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-6">
        <div class="w-full max-w-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-3xl p-6 sm:p-9 shadow-xl dark:shadow-2xl dark:shadow-black/80 relative overflow-hidden space-y-6 transition-colors">

            <!-- Author Header -->
            <div class="flex items-center space-x-4 border-b border-slate-200 dark:border-slate-800 pb-5">
                <div class="relative">
                    <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center font-bold text-xl text-white shadow-lg border border-blue-400/30">
                        {{ substr($author->name ?? 'Br', 0, 2) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900" title="Online"></span>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Pesan dari Pimpinan Kampus</div>
                    <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">{{ $author->name ?? 'Koordinator Sekolah' }}</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $author->job_title ?? 'Koordinator Kampus Pangudi Luhur Deltamas' }}</p>
                </div>
            </div>

            <!-- Main Greeting Message -->
            <div class="space-y-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-snug">
                    {{ $greeting->title }}
                </h2>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 text-sm leading-relaxed">
                    {{ $greeting->message }}
                </div>
            </div>

            <!-- Highlights Box (COM-01) -->
            @if($highlights->isNotEmpty())
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <i class="bi bi-info-circle-fill text-amber-500"></i>
                            <span>Highlight Informasi Krusial Hari Ini</span>
                        </span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $highlights->count() }} Poin Penting</span>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($highlights as $h)
                            <div class="p-3.5 rounded-xl border {{ $h->is_urgent ? 'bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/30' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-800' }} flex items-start space-x-3 transition">
                                <span class="h-2 w-2 rounded-full {{ $h->is_urgent ? 'bg-rose-500 animate-ping' : 'bg-blue-500' }} mt-1.5 shrink-0"></span>
                                <div class="space-y-0.5 flex-1">
                                    <div class="text-xs font-bold {{ $h->is_urgent ? 'text-rose-700 dark:text-rose-300' : 'text-slate-900 dark:text-white' }} flex items-center gap-2">
                                        <span>{{ $h->title }}</span>
                                        @if($h->is_urgent)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] uppercase bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-bold border border-rose-200 dark:border-rose-500/30">PENTING</span>
                                        @endif
                                    </div>
                                    @if($h->detail)
                                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ $h->detail }}</p>
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
                <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-500 active:scale-[0.99] font-bold text-sm sm:text-base text-white shadow-xl shadow-blue-600/30 transition duration-150 flex items-center justify-center space-x-2.5 group">
                    <span>{{ $greeting->cta_label ?? 'Masuk Beranda Kampus' }}</span>
                    <i class="bi bi-arrow-right font-bold transition transform group-hover:translate-x-1"></i>
                </button>
                <p class="text-center text-[11px] text-slate-400 dark:text-slate-500 mt-2.5">
                    Menekan tombol ini mencatat konfirmasi keterbacaan (read receipt) resmi ke sistem.
                </p>
            </form>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-950/40 py-3 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors">
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
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>
</html>
