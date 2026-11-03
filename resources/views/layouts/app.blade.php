<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIAP PL Deltamas')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>

    <script>
        // Init theme immediately to prevent flash
        if (localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col justify-between antialiased transition-colors duration-200 selection:bg-blue-600 selection:text-white">

    <!-- Top App Bar -->
    <header class="border-b border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 backdrop-blur sticky top-0 z-50 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo" class="h-10 w-10 object-contain">
                    <div>
                        <h1 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            SIAP PL Deltamas
                        </h1>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            @if(isset($user) && $user->units->isNotEmpty())
                                Unit {{ $user->units->pluck('code')->implode(' & ') }}
                            @else
                                Tingkat Kampus
                            @endif
                        </p>
                    </div>
                </a>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Theme Toggle Button -->
                <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Ganti Tema Terang/Gelap">
                    <i id="themeIconSun" class="bi bi-sun-fill text-amber-500 hidden dark:inline"></i>
                    <i id="themeIconMoon" class="bi bi-moon-stars-fill text-slate-600 dark:hidden inline"></i>
                </button>

                @auth
                    <a href="{{ route('announcements.index') }}" class="px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-600/20 hover:bg-blue-100 dark:hover:bg-blue-600/30 border border-blue-200 dark:border-blue-500/40 text-blue-700 dark:text-blue-300 text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="bi bi-megaphone-fill text-blue-600 dark:text-blue-400"></i>
                        <span class="hidden sm:inline">Papan Siaran</span>
                    </a>

                    <a href="{{ route('agendas.index') }}" class="px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-600/20 hover:bg-emerald-100 dark:hover:bg-emerald-600/30 border border-emerald-200 dark:border-emerald-500/40 text-emerald-700 dark:text-emerald-300 text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="bi bi-calendar3 text-emerald-600 dark:text-emerald-400"></i>
                        <span class="hidden sm:inline">Agenda</span>
                    </a>

                    <a href="{{ route('leaves.index') }}" class="px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-600/20 hover:bg-amber-100 dark:hover:bg-amber-600/30 border border-amber-200 dark:border-amber-500/40 text-amber-700 dark:text-amber-300 text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="bi bi-file-earmark-check-fill text-amber-600 dark:text-amber-400"></i>
                        <span class="hidden sm:inline">Perizinan</span>
                    </a>

                    <a href="{{ route('facilities.index') }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-600/20 hover:bg-indigo-100 dark:hover:bg-indigo-600/30 border border-indigo-200 dark:border-indigo-500/40 text-indigo-700 dark:text-indigo-300 text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="bi bi-building text-indigo-600 dark:text-indigo-400"></i>
                        <span class="hidden sm:inline">Fasilitas</span>
                    </a>

                    <a href="{{ route('operational.index') }}" class="px-3 py-1.5 rounded-lg bg-cyan-50 dark:bg-cyan-600/20 hover:bg-cyan-100 dark:hover:bg-cyan-600/30 border border-cyan-200 dark:border-cyan-500/40 text-cyan-700 dark:text-cyan-300 text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="bi bi-tools text-cyan-600 dark:text-cyan-400"></i>
                        <span class="hidden sm:inline">Operasional</span>
                    </a>

                    <a href="{{ route('letters.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="bi bi-envelope-paper text-slate-600 dark:text-slate-400"></i>
                        <span class="hidden sm:inline">Surat</span>
                    </a>

                    <div class="hidden md:flex flex-col text-right pl-2 border-l border-slate-200 dark:border-slate-800">
                        <span class="text-xs font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ Auth::user()->nip ?? Auth::user()->email }}</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs font-semibold transition flex items-center gap-1.5">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Yield -->
    <main class="flex-1 w-full">
        @yield('content')
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
    </script>
</body>
</html>
