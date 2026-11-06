<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sapaan Koordinator — SIAP PL Deltamas</title>
    @include('partials.design-head')
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#102433">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIAP PL">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased selection:bg-blue-800 selection:text-white dark:bg-slate-950 dark:text-slate-100">
    <header class="border-b border-slate-200 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-950/80">
        <div class="mx-auto flex h-16 max-w-3xl items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <span class="grid h-9 w-9 place-items-center rounded-full bg-white p-0.5 ring-1 ring-slate-200 dark:ring-white/20">
                    <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo PL Deltamas" class="h-full w-full rounded-full object-contain" />
                </span>
                <span class="text-sm font-semibold text-slate-900 dark:text-white">SIAP PL Deltamas</span>
                <span class="rounded-full border border-amber-300 bg-amber-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">Sapaan Intersepsi</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleTheme()" class="grid h-9 w-9 place-items-center rounded-full border border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" title="Ganti Tema">
                    <i class="bi bi-sun-fill hidden text-amber-500 dark:inline"></i>
                    <i class="bi bi-moon-stars-fill inline dark:hidden"></i>
                </button>
                <span class="hidden font-mono text-xs text-slate-500 sm:block">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </header>

    <main class="flex flex-1 items-center justify-center px-4 py-8">
        <article class="w-full max-w-2xl space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-9">
            <div class="flex items-center gap-4 border-b border-slate-200 pb-5 dark:border-slate-800">
                <div class="relative">
                    <div class="grid h-16 w-16 place-items-center rounded-2xl bg-ink-900 text-xl font-semibold text-gold-300">
                        {{ substr($author->name ?? 'Br', 0, 2) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border-2 border-white bg-emerald-500 dark:border-slate-900" title="Online"></span>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gold-600 dark:text-gold-400">Pesan dari Pimpinan Kampus</p>
                    <h1 class="font-display text-xl text-slate-900 dark:text-white sm:text-2xl">{{ $author->name ?? 'Koordinator Sekolah' }}</h1>
                    <p class="text-xs text-slate-500">{{ $author->job_title ?? 'Koordinator Kampus Pangudi Luhur Deltamas' }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <h2 class="font-display text-2xl leading-snug text-slate-900 dark:text-white sm:text-3xl">{{ $greeting->title }}</h2>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm leading-relaxed text-slate-700 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">
                    {{ $greeting->message }}
                </div>
            </div>

            @if($highlights->isNotEmpty())
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <i class="bi bi-info-circle-fill text-amber-500"></i>
                            <span>Highlight Informasi Krusial Hari Ini</span>
                        </span>
                        <span class="font-mono text-[10px] text-slate-400">{{ $highlights->count() }} Poin Penting</span>
                    </div>
                    <div class="space-y-2.5">
                        @foreach($highlights as $h)
                            <div class="flex items-start gap-3 rounded-xl border p-3.5 {{ $h->is_urgent ? 'border-rose-200 bg-rose-50 dark:border-rose-500/30 dark:bg-rose-500/10' : 'border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950' }}">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $h->is_urgent ? 'animate-ping bg-rose-500' : 'bg-blue-600' }}"></span>
                                <div class="flex-1 space-y-0.5">
                                    <div class="flex items-center gap-2 text-xs font-bold {{ $h->is_urgent ? 'text-rose-700 dark:text-rose-300' : 'text-slate-900 dark:text-white' }}">
                                        <span>{{ $h->title }}</span>
                                        @if($h->is_urgent)
                                            <span class="rounded border border-rose-200 bg-rose-100 px-1.5 py-0.5 text-[9px] font-bold uppercase text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/20 dark:text-rose-300">PENTING</span>
                                        @endif
                                    </div>
                                    @if($h->detail)
                                        <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-300">{{ $h->detail }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('greeting.acknowledge') }}" method="POST" class="pt-2">
                @csrf
                <input type="hidden" name="greeting_id" value="{{ $greeting->id }}">
                <button type="submit" class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-ink-900 py-4 text-sm font-semibold text-white transition hover:bg-ink-800 sm:text-base">
                    <span>{{ $greeting->cta_label ?? 'Masuk Beranda Kampus' }}</span>
                    <i class="bi bi-arrow-right font-bold transition group-hover:translate-x-1"></i>
                </button>
                <p class="mt-2.5 text-center text-[11px] text-slate-400">
                    Menekan tombol ini mencatat konfirmasi keterbacaan (read receipt) resmi ke sistem.
                </p>
            </form>
        </article>
    </main>

    <footer class="border-t border-slate-200 py-3 text-center text-xs text-slate-500 dark:border-slate-800">
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
