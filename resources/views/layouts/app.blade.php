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
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
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
    <header class="border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur sticky top-0 z-40 transition-colors shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Left: Logo & Portal Title -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                    <img src="{{ asset('images/logo-pangudi-luhur.png') }}" alt="Logo" class="h-9 w-9 sm:h-10 sm:w-10 object-contain">
                    <div>
                        <h1 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                            SIAP PL Deltamas
                        </h1>
                        <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate max-w-[170px] sm:max-w-xs">
                            @if(isset($user) && $user->units->isNotEmpty())
                                Unit {{ $user->units->pluck('code')->implode(' & ') }}
                            @else
                                Kampus Deltamas
                            @endif
                        </p>
                    </div>
                </a>
            </div>

            <!-- Right: Theme Toggle & Hamburger Button -->
            <div class="flex items-center space-x-2">
                <!-- Theme Toggle Button -->
                <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition" title="Ganti Tema">
                    <i id="themeIconSun" class="bi bi-sun-fill text-amber-500 hidden dark:inline"></i>
                    <i id="themeIconMoon" class="bi bi-moon-stars-fill text-slate-600 dark:hidden inline"></i>
                </button>

                @auth
                    <!-- Hamburger Menu Button -->
                    <button type="button" onclick="toggleNavDrawer()" class="p-2 sm:px-3 sm:py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-md shadow-blue-600/20" title="Buka Menu Navigasi">
                        <i class="bi bi-list text-lg leading-none"></i>
                        <span class="hidden sm:inline">Menu</span>
                    </button>
                @endauth
            </div>
        </div>
    </header>

    @auth
    <!-- Slide-over Off-canvas Drawer Navigation (Hamburger Menu) -->
    <div id="navDrawerBackdrop" onclick="toggleNavDrawer()" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none"></div>

    <aside id="navDrawer" class="fixed top-0 right-0 bottom-0 w-80 max-w-[85vw] bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 z-50 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col justify-between">
        
        <!-- Drawer Content -->
        <div>
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-white shadow-md">
                        <i class="bi bi-grid-fill"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">Menu Navigasi</h2>
                        <span class="text-[10px] uppercase font-semibold text-blue-600 dark:text-blue-400">SIAP PL Deltamas</span>
                    </div>
                </div>
                <button type="button" onclick="toggleNavDrawer()" class="p-2 rounded-lg text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="bi bi-x-lg text-base"></i>
                </button>
            </div>

            <!-- Profile Info in Drawer -->
            <div class="p-4 mx-4 mt-4 rounded-2xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center text-sm shadow">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono truncate">{{ Auth::user()->nip ?? Auth::user()->email }}</div>
                        <span class="inline-block mt-0.5 px-2 py-0.2 rounded text-[9px] uppercase font-bold bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300">
                            {{ ucfirst(str_replace('_', ' ', Auth::user()->getRoleNames()->first() ?? 'Staf')) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Nav Links List -->
            <nav class="p-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-280px)]">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-house-door-fill text-base w-5 text-center"></i>
                    <span>Dashboard Beranda</span>
                </a>

                <a href="{{ route('announcements.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('announcements.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-megaphone-fill text-base w-5 text-center text-blue-500"></i>
                    <span>Papan Siaran (Broadcast)</span>
                </a>

                <a href="{{ route('agendas.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('agendas.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-calendar3 text-base w-5 text-center text-emerald-500"></i>
                    <span>Kalender Agenda Kampus</span>
                </a>

                <a href="{{ route('leaves.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('leaves.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-file-earmark-check-fill text-base w-5 text-center text-amber-500"></i>
                    <span>Perizinan & Guru Inval</span>
                </a>

                <a href="{{ route('facilities.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('facilities.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-building text-base w-5 text-center text-indigo-500"></i>
                    <span>Fasilitas & E-Ticket QR</span>
                </a>

                <a href="{{ route('operational.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('operational.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-tools text-base w-5 text-center text-cyan-500"></i>
                    <span>Operasional Lapangan</span>
                </a>

                <a href="{{ route('letters.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('letters.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    <i class="bi bi-envelope-paper-fill text-base w-5 text-center text-slate-400"></i>
                    <span>Tata Persuratan & Arsip</span>
                </a>

                <!-- Kurikulum Drawer Button -->
                <button type="button" onclick="showDevelopmentNotice()" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition text-left group">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-book-half text-base w-5 text-center text-blue-600 dark:text-blue-400"></i>
                        <span>Kurikulum</span>
                    </div>
                </button>

                <div class="pt-2 border-t border-slate-200 dark:border-slate-800 mt-2">
                    <a href="{{ route('greeting.show') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="bi bi-chat-quote-fill text-amber-500 w-5 text-center"></i>
                        <span>Sapaan Koordinator</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Drawer Footer: Logout Button -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 text-xs font-bold transition flex items-center justify-center gap-2">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar dari Akun</span>
                </button>
            </form>
        </div>
    </aside>
    @endauth

    <!-- Main Content Yield -->
    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <!-- Modal Pemberitahuan Pengembangan (Kurikulum) -->
    <div id="devNoticeModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden transition-opacity">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 w-full max-w-sm space-y-4 shadow-2xl text-center">
            <div class="h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-500/20 border-2 border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            
            <div class="space-y-1.5">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Modul Kurikulum</h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed pt-1">
                    Fitur ini <strong class="text-slate-900 dark:text-white">masih dalam pengembangan</strong>.
                </p>
            </div>

            <div class="pt-2">
                <button type="button" onclick="closeDevelopmentNotice()" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 font-bold text-xs text-white shadow-lg shadow-blue-600/30 transition">
                    Mengerti & Tutup
                </button>
            </div>
        </div>
    </div>

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

        function toggleNavDrawer() {
            const drawer = document.getElementById('navDrawer');
            const backdrop = document.getElementById('navDrawerBackdrop');
            if (!drawer || !backdrop) return;

            const isClosed = drawer.classList.contains('translate-x-full');
            if (isClosed) {
                drawer.classList.remove('translate-x-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100');
            } else {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100');
            }
        }

        function showDevelopmentNotice() {
            const modal = document.getElementById('devNoticeModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
            const drawer = document.getElementById('navDrawer');
            const backdrop = document.getElementById('navDrawerBackdrop');
            if (drawer && !drawer.classList.contains('translate-x-full')) {
                drawer.classList.add('translate-x-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100');
            }
        }

        function closeDevelopmentNotice() {
            const modal = document.getElementById('devNoticeModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
